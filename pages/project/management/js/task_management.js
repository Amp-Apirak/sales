// ตัวแปรสำหรับเก็บข้อมูล tasks ทั้งหมด
let allTasks = [];

// โหลดข้อมูล tasks เมื่อโหลดหน้า
// อัปเดตฟังก์ชัน loadTasks()
function loadTasks() {
  const projectId = "<?php echo $project_id; ?>"; // รับค่า project_id จาก PHP

  // เพิ่ม debug info
  $("#taskDebugInfo").html("Loading tasks for project ID: " + projectId);

  $.ajax({
    url: "../../management/api/get_tasks.php", // แก้ path ให้ถูกต้อง
    type: "GET",
    data: { project_id: projectId },
    dataType: "json",
    success: function (response) {
      console.log("API Response:", response); // debug
      $("#taskDebugInfo").html("Loaded tasks successfully");

      if (response.success) {
        if (response.tasks && response.tasks.length > 0) {
          allTasks = response.tasks;
          renderTasks();
        } else {
          $("#tasksTableBody").html(
            '<tr><td colspan="7" class="text-center">ไม่พบข้อมูลงาน</td></tr>'
          );
        }
      } else {
        $("#taskDebugInfo").html("Error: " + response.message);
        showError(response.message || "ไม่สามารถโหลดข้อมูลได้");
      }
    },
    error: function (xhr, status, error) {
      console.error("Ajax Error:", { xhr, status, error });
      $("#taskDebugInfo").html("Ajax Error: " + error);
      showError("ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้");
    },
  });
}

// เพิ่ม event listener เมื่อคลิกที่แท็บ
$(document).ready(function () {
  // เพิ่ม event listener สำหรับการคลิกที่แท็บ
  $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
    if ($(e.target).attr("href") === "#tasks") {
      loadTasks();
    }
  });

  // โหลดข้อมูลเริ่มต้นถ้าอยู่ที่แท็บ tasks
  if (window.location.hash === "#tasks") {
    loadTasks();
  }
});
// แสดงข้อมูล tasks ในตาราง
function renderTasks(tasks = allTasks, level = 0) {
  console.log("Rendering tasks:", { tasks, level }); // debug

  const tbody = $("#tasksTableBody");
  if (level === 0) tbody.empty();

  tasks.forEach((task) => {
    console.log("Processing task:", task); // debug

    // สร้างแถวสำหรับ task
    const row = $("<tr>").addClass("task-row");
    if (level > 0) {
      row.addClass("sub-task");
      row.css("padding-left", level * 20 + "px");
    }

    // สร้าง HTML สำหรับแถว
    row.html(`
          <td>
              <div style="margin-left: ${level * 20}px">
                  ${
                    task.sub_tasks && task.sub_tasks.length > 0
                      ? `<i class="fas fa-caret-down mr-2 toggle-subtasks" style="cursor: pointer;"></i>`
                      : `<i class="fas fa-tasks mr-2"></i>`
                  }
                  ${escapeHtml(task.task_name)}
              </div>
          </td>
          <td>${formatDate(task.start_date) || "-"}</td>
          <td>${formatDate(task.end_date) || "-"}</td>
          <td>${escapeHtml(task.assignee_names) || "-"}</td>
          <td>${getStatusBadge(task.status)}</td>
          <td>
              <div class="progress">
                  <div class="progress-bar" role="progressbar" 
                       style="width: ${task.progress || 0}%" 
                       aria-valuenow="${task.progress || 0}" 
                       aria-valuemin="0" 
                       aria-valuemax="100">
                      ${task.progress || 0}%
                  </div>
              </div>
          </td>
          <td>
              <button class="btn btn-sm btn-info mr-1" onclick="editTask('${
                task.task_id
              }')">
                  <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-success mr-1" onclick="addSubTask('${
                task.task_id
              }')">
                  <i class="fas fa-plus"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="deleteTask('${
                task.task_id
              }')">
                  <i class="fas fa-trash"></i>
              </button>
          </td>
      `);

    tbody.append(row);

    // แสดง sub-tasks ถ้ามี
    if (task.sub_tasks && task.sub_tasks.length > 0) {
      renderTasks(task.sub_tasks, level + 1);
    }
  });
}

// ฟังก์ชันช่วยต่างๆ
function escapeHtml(text) {
  if (!text) return "";
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

function formatDate(dateString) {
  if (!dateString) return "-";
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString("th-TH", options);
}

function getStatusBadge(status) {
  const statusClasses = {
    Pending: "badge-warning",
    "In Progress": "badge-primary",
    Completed: "badge-success",
    Cancelled: "badge-danger",
  };
  const statusNames = {
    Pending: "รอดำเนินการ",
    "In Progress": "กำลังดำเนินการ",
    Completed: "เสร็จสิ้น",
    Cancelled: "ยกเลิก",
  };
  return `<span class="badge ${statusClasses[status]}">${statusNames[status]}</span>`;
}

// เปิด modal เพิ่มงานใหม่
function openAddTaskModal(parentTaskId = null) {
  $("#taskId").val("");
  $("#parentTaskId").val(parentTaskId);
  $("#taskForm")[0].reset();
  $("#taskModalTitle").text(parentTaskId ? "เพิ่มงานย่อย" : "เพิ่มงานใหม่");
  $("#taskModal").modal("show");
}

// แก้ไขงาน
function editTask(taskId) {
  const task = findTask(taskId);
  if (task) {
    $("#taskId").val(task.task_id);
    $("#taskName").val(task.task_name);
    $("#taskDescription").val(task.description);
    $("#startDate").val(task.start_date);
    $("#endDate").val(task.end_date);
    $("#status").val(task.status);
    $("#priority").val(task.priority);
    $("#progress").val(task.progress);
    // ตั้งค่าผู้รับผิดชอบ (ต้องมีการโหลดข้อมูลผู้ใช้ก่อน)
    $("#taskModalTitle").text("แก้ไขงาน");
    $("#taskModal").modal("show");
  }
}

// ค้นหา task จาก ID
function findTask(taskId, tasks = allTasks) {
  for (let task of tasks) {
    if (task.task_id === taskId) return task;
    if (task.sub_tasks.length > 0) {
      const foundTask = findTask(taskId, task.sub_tasks);
      if (foundTask) return foundTask;
    }
  }
  return null;
}

// บันทึกงาน
function saveTask() {
  // ตรวจสอบข้อมูลก่อนบันทึก
  if (!validateTaskForm()) return;

  // รวบรวมข้อมูลจากฟอร์ม
  const taskData = {
    project_id: currentProjectId,
    task_id: $("#taskId").val(),
    project_id: projectId,
    parent_task_id: $("#parentTaskId").val(),
    task_name: $("#taskName").val(),
    description: $("#taskDescription").val(),
    start_date: $("#startDate").val(),
    end_date: $("#endDate").val(),
    status: $("#status").val(),
    priority: $("#priority").val(),
    progress: $("#progress").val(),
    assignee: $("#assignee").val(),
  };

  // ส่งข้อมูลไปบันทึก
  $.ajax({
    url: "management/api/save_task.php",
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify(taskData),
    success: function (response) {
      if (response.success) {
        showSuccess(response.message);
        $("#taskModal").modal("hide");
        loadTasks(); // โหลดข้อมูลใหม่
      } else {
        showError(response.message);
      }
    },
    error: function () {
      showError("ไม่สามารถบันทึกข้อมูลได้");
    },
  });
}

// ตรวจสอบข้อมูลในฟอร์ม
function validateTaskForm() {
  if (!$("#taskName").val().trim()) {
    showError("กรุณากรอกชื่องาน");
    return false;
  }

  const startDate = new Date($("#startDate").val());
  const endDate = new Date($("#endDate").val());

  if (startDate && endDate && startDate > endDate) {
    showError("วันที่สิ้นสุดต้องมากกว่าวันที่เริ่ม");
    return false;
  }

  return true;
}

// ลบงาน
function deleteTask(taskId) {
  Swal.fire({
    title: "ยืนยันการลบ",
    text: "คุณต้องการลบงานนี้ใช่หรือไม่?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "ใช่, ลบเลย!",
    cancelButtonText: "ยกเลิก",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "management/api/delete_task.php",
        type: "POST",
        data: {
          task_id: taskId,
        },
        success: function (response) {
          if (response.success) {
            showSuccess("ลบงานสำเร็จ");
            loadTasks();
          } else {
            showError(response.message);
          }
        },
        error: function () {
          showError("ไม่สามารถลบข้อมูลได้");
        },
      });
    }
  });
}

// แสดงข้อความสำเร็จ
function showSuccess(message) {
  Swal.fire({
    icon: "success",
    title: "สำเร็จ",
    text: message,
    timer: 2000,
  });
}

// แสดงข้อความผิดพลาด
function showError(message) {
  Swal.fire({
    icon: "error",
    title: "เกิดข้อผิดพลาด",
    text: message,
  });
}

// เพิ่ม Event Listeners
$(document).ready(function () {
  loadTasks();

  // Toggle sub-tasks
  $(document).on("click", ".toggle-subtasks", function (e) {
    e.stopPropagation();
    const icon = $(this);
    const row = icon.closest("tr");
    const level = row.data("level");
    const nextRows = row.nextUntil(`tr[data-level="${level}"]`);

    if (icon.hasClass("fa-caret-down")) {
      icon.removeClass("fa-caret-down").addClass("fa-caret-right");
      nextRows.hide();
    } else {
      icon.removeClass("fa-caret-right").addClass("fa-caret-down");
      nextRows.show();
    }
  });

  // Initialize Select2 for assignee dropdown
  $("#assignee").select2({
    theme: "bootstrap4",
    placeholder: "เลือกผู้รับผิดชอบ",
    allowClear: true,
  });

  // Reset form when modal is closed
  $("#taskModal").on("hidden.bs.modal", function () {
    $("#taskForm")[0].reset();
    $("#taskId").val("");
    $("#parentTaskId").val("");
  });
});
