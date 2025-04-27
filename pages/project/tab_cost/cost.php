<!-- 6.  // ฟังก์ชันเพิ่มแถวใหม่ในตารางต้นทุน -->
<script>
    // ฟังก์ชันคำนวณยอดรวม
    document.getElementById('qtyInput').addEventListener('input', calculateTotals);
    document.getElementById('priceInput').addEventListener('input', handleInputWithCommas);
    document.getElementById('costInput').addEventListener('input', handleInputWithCommas);

    // ฟังก์ชันฟอร์แมตตัวเลขพร้อมรักษาตำแหน่ง Cursor
    function handleInputWithCommas(event) {
        const input = event.target;
        let value = input.value;

        // เก็บตำแหน่งของ Cursor ก่อนฟอร์แมต
        const cursorPosition = input.selectionStart;

        // ลบคอมม่าออกจากค่าที่มีอยู่
        value = value.replace(/,/g, '');

        // ตรวจสอบและฟอร์แมตตัวเลขใหม่
        if (!isNaN(value) && value !== '') {
            input.value = formatNumber(value);
        } else {
            input.value = '';
        }

        // คืนตำแหน่ง Cursor กลับ
        input.setSelectionRange(cursorPosition, cursorPosition);
        calculateTotals(); // เรียกฟังก์ชันคำนวณยอดรวมใหม่
    }

    // ฟังก์ชันฟอร์แมตตัวเลขให้มีคอมม่า
    function formatNumber(value) {
        const parts = value.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return parts.join('.');
    }

    // ฟังก์ชันคำนวณยอดรวมในแถวใหม่ที่กรอกข้อมูล
    function calculateTotals() {
        const qty = parseFloat(document.getElementById('qtyInput').value.replace(/,/g, '')) || 0;
        const price = parseFloat(document.getElementById('priceInput').value.replace(/,/g, '')) || 0;
        const cost = parseFloat(document.getElementById('costInput').value.replace(/,/g, '')) || 0;

        const totalAmount = qty * price;
        const totalCost = qty * cost;

        document.getElementById('totalAmountInput').textContent = totalAmount.toLocaleString('th-TH', {
            minimumFractionDigits: 2
        });
        document.getElementById('totalCostInput').textContent = totalCost.toLocaleString('th-TH', {
            minimumFractionDigits: 2
        });
    }

    // ฟังก์ชันสำหรับเพิ่มแถวใหม่ในตาราง
    function addRow() {
        const type = document.getElementById('typeInput').value;
        const partNo = document.getElementById('partNoInput').value;
        const description = document.getElementById('descriptionInput').value;
        const qty = parseFloat(document.getElementById('qtyInput').value.replace(/,/g, '')) || 0;
        const price = parseFloat(document.getElementById('priceInput').value.replace(/,/g, '')) || 0;
        const totalAmount = qty * price;
        const cost = parseFloat(document.getElementById('costInput').value.replace(/,/g, '')) || 0;
        const totalCost = qty * cost;
        const supplier = document.getElementById('supplierInput').value;

        if (type && partNo && description && qty && price && cost && supplier) {
            const table = document.getElementById('costTable').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow(-1); // แทรกก่อนแถวฟอร์มเพิ่ม

            newRow.innerHTML = `
            <tr>
                <td>${type}</td>
                <td>${partNo}</td>
                <td>${description}</td>
                <td>${qty}</td>
                <td>${price.toLocaleString('th-TH', { minimumFractionDigits: 2 })}</td>
                <td>${totalAmount.toLocaleString('th-TH', { minimumFractionDigits: 2 })}</td>
                <td>${cost.toLocaleString('th-TH', { minimumFractionDigits: 2 })}</td>
                <td>${totalCost.toLocaleString('th-TH', { minimumFractionDigits: 2 })}</td>
                <td>${supplier}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editRow(this)">แก้ไข</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteRow(this)">ลบ</button>
                </td>
            </tr>
        `;

            updateTotals();
            clearFormFields();
        } else {
            alert("กรุณากรอกข้อมูลให้ครบถ้วน");
        }
    }

    // ฟังก์ชันสำหรับแก้ไขแถว
    function editRow(button) {
        const row = button.closest('tr');
        const cells = row.getElementsByTagName('td');

        // นำข้อมูลจากแถวที่เลือกไปยังฟิลด์ฟอร์มเพื่อแก้ไข
        document.getElementById('typeInput').value = cells[0].textContent;
        document.getElementById('partNoInput').value = cells[1].textContent;
        document.getElementById('descriptionInput').value = cells[2].textContent;
        document.getElementById('qtyInput').value = cells[3].textContent;
        document.getElementById('priceInput').value = cells[4].textContent.replace(/,/g, '');
        document.getElementById('costInput').value = cells[6].textContent.replace(/,/g, '');
        document.getElementById('supplierInput').value = cells[8].textContent;

        // ลบแถวปัจจุบันหลังจากแก้ไขเสร็จ
        deleteRow(button);
    }

    // ฟังก์ชันสำหรับลบแถว
    function deleteRow(button) {
        const row = button.closest('tr');
        row.parentNode.removeChild(row);
        updateTotals();
    }

    // ฟังก์ชันสำหรับคำนวณยอดรวม
    function updateTotals() {
        const rows = document.querySelectorAll('#costTable tbody tr:not(:last-child)'); // เว้นแถวสุดท้าย
        let totalAmount = 0;
        let totalCost = 0;

        rows.forEach(row => {
            const amountCell = row.cells[5];
            const costCell = row.cells[7];

            totalAmount += parseFloat(amountCell.textContent.replace(/,/g, '')) || 0;
            totalCost += parseFloat(costCell.textContent.replace(/,/g, '')) || 0;
        });

        document.getElementById('totalAmount').textContent = totalAmount.toLocaleString('th-TH', {
            minimumFractionDigits: 2
        });
        document.getElementById('vatAmount').textContent = (totalAmount * 0.07).toLocaleString('th-TH', {
            minimumFractionDigits: 2
        }); // คำนวณ VAT 7%
        document.getElementById('grandTotal').textContent = (totalAmount + (totalAmount * 0.07)).toLocaleString('th-TH', {
            minimumFractionDigits: 2
        });
    }

    // ฟังก์ชันสำหรับล้างข้อมูลในฟอร์ม
    function clearFormFields() {
        document.getElementById('typeInput').value = '';
        document.getElementById('partNoInput').value = '';
        document.getElementById('descriptionInput').value = '';
        document.getElementById('qtyInput').value = '';
        document.getElementById('priceInput').value = '';
        document.getElementById('costInput').value = '';
        document.getElementById('supplierInput').value = '';
    }


    // เพิ่ม global variables
    let projectId = '<?php echo $project_id; ?>'; // รับค่า project_id จาก PHP

    // เมื่อโหลดหน้าเว็บ
    $(document).ready(function() {
        // โหลดข้อมูลเริ่มต้น
        loadCosts();

        // เพิ่ม event listeners
        $('#qtyInput').on('input', calculateTotals);
        $('#priceInput').on('input', handleInputWithCommas);
        $('#costInput').on('input', handleInputWithCommas);
    });

    // แทนที่ฟังก์ชัน addRow เดิมด้วย saveCost
    function saveCost() {
        if (!validateInputs()) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                text: 'โปรดกรอกข้อมูลที่จำเป็นทุกช่อง',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        const costData = {
            csrf_token: $('input[name="csrf_token"]').val(),
            project_id: projectId,
            type: $('#typeInput').val(),
            part_no: $('#partNoInput').val(),
            description: $('#descriptionInput').val(),
            quantity: parseFloat($('#qtyInput').val()),
            unit: $('#unitInput').val(),
            price_per_unit: parseFormattedNumber($('#priceInput').val()),
            cost_per_unit: parseFormattedNumber($('#costInput').val()),
            supplier: $('#supplierInput').val()
        };

        $.ajax({
            url: 'save_cost.php',
            type: 'POST',
            data: costData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ',
                        text: 'เพิ่มข้อมูลต้นทุนเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        clearFormFields(); // ล้างฟอร์ม
                        loadCosts(); // โหลดข้อมูลใหม่
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message,
                        confirmButtonText: 'ตกลง'
                    });
                }
            }
        });
    }

    // ฟังก์ชันป้องกัน XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // เรียกโหลดข้อมูลเมื่อโหลดหน้า
    $(document).ready(function() {
        loadCosts();
    });

    // เพิ่มฟังก์ชันตรวจสอบการกรอกข้อมูล
    function validateInputs() {
        const required = ['typeInput', 'partNoInput', 'descriptionInput', 'qtyInput', 'unitInput', 'priceInput', 'costInput', 'supplierInput'];
        return required.every(id => $('#' + id).val().trim() !== '');
    }

    // ฟังก์ชันโหลดข้อมูลต้นทุน
    function loadCosts() {
        $.ajax({
            url: 'get_costs.php',
            type: 'GET',
            data: {
                project_id: projectId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // ถ้ามีตาราง DataTable อยู่แล้ว ให้ทำลายก่อน
                    if ($.fn.DataTable.isDataTable('#costTable')) {
                        $('#costTable').DataTable().destroy();
                    }

                    const tbody = $('#costTableBody');
                    tbody.empty(); // ล้างข้อมูลเก่า

                    // เพิ่มข้อมูลใหม่
                    response.costs.forEach(function(cost) {
                        const row = $('<tr>');
                        row.html(`
                        <td>${escapeHtml(cost.type)}</td>
                        <td>${escapeHtml(cost.part_no)}</td>
                        <td>${escapeHtml(cost.description)}</td>
                        <td>${cost.quantity}</td>
                        <td>${escapeHtml(cost.unit)}</td>
                        <td>${formatNumber(cost.price_per_unit)}</td>
                        <td>${formatNumber(cost.total_amount)}</td>
                        <?php if ($hasAccessToFinancialInfo): ?>
                            <td>${formatNumber(cost.cost_per_unit)}</td>
                            <td>${formatNumber(cost.total_cost)}</td>
                            <td>${escapeHtml(cost.supplier)}</td>
                        <?php endif; ?>
                        <td>
                            <button class="btn btn-sm btn-info mr-1" onclick="editCost('${cost.cost_id}')">แก้ไข</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCost('${cost.cost_id}')">ลบ</button>
                        </td>
                    `);
                        tbody.append(row);
                    });

                    // สร้าง DataTable พร้อมปุ่ม Export
                    $('#costTable').DataTable({
                        dom: 'Bfrtip',
                        buttons: [{
                                extend: 'excel',
                                text: '<i class="fas fa-file-excel"></i> Export Excel',
                                className: 'btn btn-success btn-sm',
                                title: 'Project Cost Report',
                                filename: 'Project_Costs_' + new Date().toISOString().slice(0, 10),
                                customize: function(xlsx) {
                                    var sheet = xlsx.xl.worksheets['sheet1.xml'];

                                    // เพิ่มข้อมูลสรุป
                                    var summaryData = [
                                        ['Summary'],
                                        ['Total Amount:', $('#totalAmount').text()],
                                        ['VAT Amount:', $('#vatAmount').text()],
                                        ['Grand Total:', $('#grandTotal').text()],
                                        ['Total Cost:', $('#totalCost').text()],
                                        ['Cost VAT Amount:', $('#costVatAmount').text()],
                                        ['Total Cost with VAT:', $('#totalCostWithVat').text()],
                                        ['Profit Amount:', $('#profitAmount').text()],
                                        ['Profit Percentage:', $('#profitPercentage').text()]
                                    ];

                                    // คำนวณตำแหน่งแถวสุดท้าย
                                    var lastRow = $('row', sheet).length;

                                    // เพิ่มข้อมูลสรุป
                                    summaryData.forEach(function(data) {
                                        lastRow++;
                                        var row = sheet.createElement('row');

                                        data.forEach(function(text, index) {
                                            var cell = sheet.createElement('c');
                                            var t = sheet.createElement('t');
                                            t.textContent = text;
                                            cell.appendChild(t);
                                            if (index === 0) {
                                                cell.setAttribute('s', '2'); // style สำหรับหัวข้อ
                                            }
                                            row.appendChild(cell);
                                        });

                                        sheet.getElementsByTagName('sheetData')[0].appendChild(row);
                                    });
                                },
                                exportOptions: {
                                    columns: ':not(:last-child)' // ไม่รวมคอลัมน์ Actions
                                }
                            },
                            // *** ปุ่ม Print เพิ่มเข้ามาใหม่ ***
                            {
                                text: '<i class="fas fa-print"></i> Print',
                                className: 'btn btn-primary btn-sm',
                                action: function(e, dt, node, config) {
                                    // เปิดหน้าต่างใหม่ cost_viewprint.php
                                    // พร้อมส่ง project_id ที่เข้ารหัสอย่างปลอดภัย
                                    window.open(
                                        'cost_viewprint.php?project_id=<?php echo urlencode(encryptUserId($project_id)); ?>',
                                        '_blank'
                                    );
                                }
                            }
                        ],
                        pageLength: 10,
                        responsive: false,
                        ordering: true,
                        searching: true,
                        columnDefs: [{
                            targets: -1, // คอลัมน์สุดท้าย (Actions)
                            orderable: false
                        }],
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json'
                        }
                    });

                    // อัพเดทข้อมูลสรุป
                    if (response.summary) {
                        updateSummaryDisplay(response.summary);
                    }
                }
            }
        });
    }


    $(document).ready(function() {
        loadCosts(); // โหลดข้อมูลและสร้าง DataTable เมื่อหน้าเว็บโหลดเสร็จ

        // เพิ่ม event listeners
        $('#qtyInput').on('input', calculateTotals);
        $('#priceInput').on('input', handleInputWithCommas);
        $('#costInput').on('input', handleInputWithCommas);
    });

    // ฟังก์ชันอัพเดทยอดรวม
    // function updateSummary(summary) {
    //     $('#totalAmount').text(formatNumber(summary.total_amount) + ' บาท');
    //     $('#vatAmount').text(formatNumber(summary.vat_amount) + ' บาท');
    //     $('#grandTotal').text(formatNumber(summary.grand_total) + ' บาท');
    //     $('#totalCost').text(formatNumber(summary.total_cost) + ' บาท');
    //     $('#costVatAmount').text(formatNumber(summary.cost_vat_amount) + ' บาท');
    //     $('#totalCostWithVat').text(formatNumber(summary.total_cost_with_vat) + ' บาท');
    //     $('#profitAmount').text(formatNumber(summary.profit_amount) + ' บาท');
    //     $('#profitPercentage').text(formatNumber(summary.profit_percentage) + '%');
    // }

    // ฟังก์ชันอัพเดทการแสดงผลสรุป
    function updateSummaryDisplay(summary) {
        $('#totalAmount').text(formatNumber(summary.total_amount));
        $('#vatAmount').text(formatNumber(summary.vat_amount));
        $('#grandTotal').text(formatNumber(summary.grand_total));
        $('#totalCost').text(formatNumber(summary.total_cost));
        $('#costVatAmount').text(formatNumber(summary.cost_vat_amount));
        $('#totalCostWithVat').text(formatNumber(summary.total_cost_with_vat));
        $('#profitAmount').text(formatNumber(summary.profit_amount));
        $('#profitPercentage').text(formatNumber(summary.profit_percentage));
    }

    // ฟังก์ชันแก้ไขข้อมูล
    function editCost(costId) {
        $.ajax({
            url: 'get_cost_details.php',
            type: 'GET',
            data: {
                cost_id: costId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const cost = response.cost;
                    // นำข้อมูลไปแสดงในฟอร์ม
                    $('#typeInput').val(cost.type);
                    $('#partNoInput').val(cost.part_no);
                    $('#descriptionInput').val(cost.description);
                    $('#qtyInput').val(cost.quantity);
                    $('#unitInput').val(cost.unit);
                    $('#priceInput').val(formatNumber(cost.price_per_unit));
                    $('#costInput').val(formatNumber(cost.cost_per_unit));
                    $('#supplierInput').val(cost.supplier);

                    // เปลี่ยนปุ่มบันทึกเป็นปุ่มอัพเดท
                    const saveButton = $('button[onclick="saveCost()"]');
                    saveButton.text('อัพเดท');
                    saveButton.attr('onclick', `updateCost('${costId}')`);

                    // เลื่อนไปที่ฟอร์ม
                    $('html, body').animate({
                        scrollTop: $('#costForm').offset().top
                    }, 500);
                }
            }
        });
    }

    // ฟังก์ชันอัพเดทข้อมูล
    function updateCost(costId) {
        if (!validateInputs()) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                text: 'โปรดกรอกข้อมูลที่จำเป็นทุกช่อง',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        const costData = {
            csrf_token: $('input[name="csrf_token"]').val(),
            cost_id: costId,
            project_id: projectId,
            type: $('#typeInput').val(),
            part_no: $('#partNoInput').val(),
            description: $('#descriptionInput').val(),
            quantity: parseFloat($('#qtyInput').val()),
            unit: $('#unitInput').val(),
            price_per_unit: parseFormattedNumber($('#priceInput').val()),
            cost_per_unit: parseFormattedNumber($('#costInput').val()),
            supplier: $('#supplierInput').val()
        };

        $.ajax({
            url: 'edit_cost.php',
            type: 'POST',
            data: costData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'อัพเดทสำเร็จ',
                        text: 'แก้ไขข้อมูลต้นทุนเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        // รีเซ็ตฟอร์มและปุ่มบันทึก
                        clearFormFields();
                        loadCosts();
                        const saveButton = $('button[onclick*="updateCost"]');
                        saveButton.text('เพิ่ม');
                        saveButton.attr('onclick', 'saveCost()');
                        loadCosts();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message,
                        confirmButtonText: 'ตกลง'
                    });
                }
            }
        });
    }

    // ฟังก์ชันลบข้อมูล
    function deleteCost(costId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบข้อมูลต้นทุนนี้ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_cost.php',
                    type: 'POST',
                    data: {
                        csrf_token: $('input[name="csrf_token"]').val(),
                        cost_id: costId,
                        project_id: projectId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: 'ลบข้อมูลต้นทุนเรียบร้อยแล้ว',
                                confirmButtonText: 'ตกลง'
                            }).then(() => {
                                loadCosts(); // โหลดข้อมูลและอัพเดทสรุปใหม่
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: response.message,
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    }
                });
            }
        });
    }
</script>
<!-- 6.  // ฟังก์ชันเพิ่มแถวใหม่ในตารางต้นทุน -->