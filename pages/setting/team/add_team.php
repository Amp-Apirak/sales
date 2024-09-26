 <!----------------------------- start Modal Add user ------------------------------->
 <?php
    if (isset($_POST['submit1'])) { /* ถ้า POST มีการกด Submit ให้ทำส่วนล่าง */
        /* ประกาศตัวแปลเก็บค่า  POST ที่รับมาจาก INPUT  */
        $contact_fullname  = $_POST['contact_fullname'];
        $contact_position  = $_POST['contact_position'];
        $contact_agency = $_POST['contact_agency'];
        $contact_tel = $_POST['contact_tel'];
        $contact_email = $_POST['contact_email'];
        $contact_detail = $_POST['contact_detail'];
        $contact_company = $_POST['contact_company'];
        $contact_type = $_POST['contact_type'];
        $contact_staff = $_POST['contact_staff'];
        $contact_province = $_POST['contact_province'];

        //print_r($_POST);
        //check duplicat
        $sql = "SELECT * From contact WHERE contact_fullname = '$contact_fullname' OR contact_email = '$contact_email' OR contact_tel = '$contact_tel' ";
        $result = $conn->query($sql);
        $num = mysqli_num_rows($result);

        // print_r($result); 
        // print_r($num);
        //ถ้า username ซ้ำ ให้เด้งกลับไปหน้าสมัครสมาชิก ปล.ข้อความใน sweetalert ปรับแต่งได้ตามความเหมาะสม
        if ($num > 0) {
            echo '<script>
                            setTimeout(function() {
                                swal({
                                        title: "The data already exists in the system.!! ",  
                                        text: "Please check the information again.",
                                        type: "warning"
                                    }, function() {
                                        window.location = "#"; //หน้าที่ต้องการให้กระโดดไป
                                        });
                                        }, 1000);
                                    </script>';
        } else {
            //ถ้า username ไม่ซ้ำ เก็บข้อมูลลงตาราง

            //sql insert
            $sql = "INSERT INTO `contact` ( `contact_fullname`,`contact_position`,`contact_agency`,
                                            `contact_tel`, `contact_email`, `contact_detail`, `contact_company`, `contact_type`, `contact_staff`,
                                            `contact_province`)
                                        VALUES ('$contact_fullname','$contact_position','$contact_agency','$contact_tel', '$contact_email',
                                            '$contact_detail', '$contact_company', '$contact_type', '$contact_staff', '$contact_province')";

            $result = $conn->query($sql);
            if ($result) {
                echo '<script>
                                                    setTimeout(function() {
                                                    swal({
                                                            title: "Save data successfully",
                                                            text: "",
                                                            type: "success"
                                                        }, function() {
                                                            window.location = "contact.php"; //หน้าที่ต้องการให้กระโดดไป
                                                            });
                                                            }, 1000);
                                                        </script>';
            } else {
                echo '<script>
                                                    setTimeout(function() {
                                                    swal({
                                                            title: "Please check the information again.",
                                                            type: "error"
                                                    }, function() {
                                                            window.location = "contact.php"; //หน้าที่ต้องการให้กระโดดไป
                                                            });
                                                            }, 1000);
                                                        </script>';
            }
            $conn = null; //close connect db
        } //else chk dup

    } //isset 
    //devbanban.com
    ?>

 <div class="modal fade" id="editbtn">
     <div class="modal-dialog editbtn">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title">Add Team</h4>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <form action="contact.php" method="POST" enctype="multipart/form-data">
                     <div class="card-body">

                         <div class="form-group">
                             <label for="team_name">Team Name<span class="text-danger">*</span></label>
                             <input type="text" name="team_name" class="form-control" id="team_name" placeholder="" required>
                         </div>
                         <!-- /.form-group -->

                         <!-- textarea -->
                         <div class="form-group">
                             <label>Description</label>
                             <textarea class="form-control" name="team_description" id="team_description" rows="4" placeholder=""></textarea>
                         </div>


                         <div class="form-group">
                             <label>Lead Team<span class="text-danger">*</span></label>
                             <select class="form-control select2" name="team_leader" required style="width: 100%;">
                                 <option selected="selected">Select</option>
                             </select>
                         </div>
                         <!-- /.form-group -->
                     </div>

             </div>
             <div class="modal-footer justify-content-between">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit1" name="submit1" value="submit1" class="btn btn-success">Save</button>
             </div>
             </form>
         </div>
         <!-- /.modal-content -->
     </div>
     <!-- /.modal-dialog -->
 </div>
 <!-- /.modal -->
 <!----------------------------- end Modal Add user --------------------------------->