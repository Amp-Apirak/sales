<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$team_id = $_SESSION['team_id'] ?? 0;
$created_by = $_SESSION['user_id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Project Management</title>
    <?php include  '../../include/header.php'; ?>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <!-- Main Sidebar Container -->
        <!-- Preloader -->
        <?php include  '../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Project Management</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Project Management v1</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- เพิ่มข้อมูล -->
                            <div class="row">
                                <!-- /.col (left) -->
                                <div class="col-md-6">
                                    <!-- /.Pipeline descriptions ----------------------------------------------------------------------->
                                    <form action="#" method="POST" enctype="multipart/form-data">
                                        <!-- /.card -->
                                        <div class="card card-primary h-100 w-100">
                                            <div class="card-header ">
                                                <h3 class="card-title">Pipeline descriptions</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col col-6">
                                                        <div class="form-group">
                                                            <label>วันเปิดการขาย</label>
                                                            <input type="date" name="date_start" class="form-control" id="exampleInputEmail1" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col col-6">
                                                        <div class="form-group">
                                                            <label>สถานะโครงการ<span class="text-danger">*</span></label>
                                                            <select class="form-control select2" name="status" id="status" style="width: 100%;" required>
                                                                <option selected="selected">Select</option>
                                                                <option>Wiating for approve</option>
                                                                <option>On-Hold</option>
                                                                <option>Quotation</option>
                                                                <option>Negotiation</option>
                                                                <option>Bidding</option>
                                                                <option>Win</option>
                                                                <option>Lost</option>
                                                            </select>
                                                        </div>
                                                        <!-- /.form-group -->
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col col-6">
                                                        <div class="form-group">
                                                            <label>วันเริ่มโครงการ</label>
                                                            <input type="date" name="date_start" class="form-control" id="exampleInputEmail1" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col col-6">
                                                        <div class="form-group">
                                                            <label>วันสิ้นสุดโครงการ</label>
                                                            <input type="date" name="date_end" class="form-control" id="exampleInputEmail1" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col col-6">
                                                        <div class="form-group">
                                                            <label>เลขที่สัญญา</label>
                                                            <input type="text" name="con_number" class="form-control" id="exampleInputEmail1" placeholder="เลขที่สัญญา">
                                                        </div>
                                                    </div>
                                                    <div class="col col-6">
                                                        <div class="form-group">
                                                            <label>สินค้าที่ขาย</label>
                                                            <select class="custom-select select2" name="product">
                                                                <option value="">Select</option>
                                                                <option value="" selected="selected">
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!-- /.form-group -->
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>ชื่อโครงการ<span class="text-danger">*</span></label>
                                                    <input type="text" name="project_name" class="form-control" id="exampleInputEmail1" placeholder="ชื่อโครงการ" required>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                            </div>
                                            <!-- /.card-body -->
                                        </div>

                                        <!-- /.Customer descriptions ----------------------------------------------------------------------->
                                        <!-- /.card -->
                                        <div class="card card-success h-100 w-100">
                                            <div class="card-header">
                                                <h3 class="card-title">Customer descriptions</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col col-12">
                                                        <div class="form-group">
                                                            <label>ข้อมูลลูกค้า</label>
                                                            <select class="custom-select select2" name="customer_id">
                                                                <option value="">Select</option>
                                                                <option value="" selected="selected">
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!-- /.form-group -->
                                                    </div>
                                                    <div class="col col-4">

                                                    </div>
                                                </div>


                                            </div>
                                            <div class="card-footer">
                                                <small>***ไม่พบข้อมูลลูกค้าสามารถเพิ่มได้ที่ เมนู "Customer"*** </small>
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                </div>
                                <!-- /.col (right) -->


                                <!-- /.Cost Project ----------------------------------------------------------------------->
                                <!-- /.col (left) -->
                                <div class="col-md-3">
                                    <!-- /.col (left) -->
                                    <div class="card card-warning h-100">
                                        <div class="card-header">
                                            <h3 class="card-title">Cost Project</h3>
                                        </div>
                                        <div class="card-body ">
                                            <div class="row">
                                                <div class="col col">
                                                    <div class="form-group">
                                                        <label>ตั้งการคำนวณ Vat (%)</label>
                                                        <select class="form-control select2" name="vat" id="vat" style="width: 100%;">
                                                            <option value="7">7%</option>
                                                            <option value="0">0%</option>
                                                            <option value="3">3%</option>
                                                            <option value="5">5%</option>

                                                        </select>
                                                    </div>
                                                    <!-- /.form-group -->

                                                    <div class="form-group">
                                                        <label>ราคาขาย/รวมภาษีมูลค่าเพิ่ม</label>
                                                        <input type="int" name="sale_vat" class="form-control" value="" id="sale_vat" placeholder="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>ราคาขาย/รวมไม่ภาษีมูลค่าเพิ่ม</label>
                                                        <input type="int" name="sale_no_vat" id="sale_no_vat" class="form-control" placeholder="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>ราคาต้นทุน/รวมภาษีมูลค่าเพิ่ม</label>
                                                        <input type="int" name="cost_vat" id="cost_vat" class="form-control" placeholder="">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>ราคาต้นทุน/รวมไม่ภาษีมูลค่าเพิ่ม</label>
                                                        <input type="int" name="cost_no_vat" class="form-control" value="" id="cost_no_vat" style="background-color:#F8F8FF" placeholder="">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>กำไรขั้นต้น/รวมไม่ภาษีมูลค่าเพิ่ม</label>
                                                        <input type="int" name="gross_profit" class="form-control" value="" id="gross_profit" style="background-color:#F8F8FF" placeholder="">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>กำไรขั้นต้น/คิดเป็น %</label>
                                                        <input type="int" name="potential" class="form-control" value="" id="potential" style="background-color:#F8F8FF" placeholder="">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">

                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.card -->

                                <!-- /.Cost Project ----------------------------------------------------------------------->

                                <div class="col-md-3">
                                    <!-- /.col (left) -->
                                    <div class="card card-warning h-100">
                                        <div class="card-header">
                                            <h3 class="card-title">Estimate Potential</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col col">
                                                    <!-- /.form-group -->
                                                    <div class="form-group">
                                                        <label>ยอดขาย/ที่คาดการณ์ไม่รวมภาษีมูลค่าเพิ่ม</label>
                                                        <input type="text" name="es_sale_no_vat" class="form-control" value="" id="es_sale_no_vat" style="background-color:#F8F8FF" placeholder="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>ต้นทุน/ที่คาดการณ์ไม่รวมภาษีมูลค่าเพิ่ม</label>
                                                        <input type="text" name="es_cost_no_vat" class="form-control" value="" id="es_cost_no_vat" style="background-color:#F8F8FF" placeholder="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>กำไรที่คาดการณ์ไม่รวมภาษีมูลค่าเพิ่ม</label>
                                                        <input type="text" name="es_gp_no_vat" class="form-control" value="" id="es_gp_no_vat" style="background-color:#F8F8FF" placeholder="">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- textarea -->
                                            <div class="form-group">
                                                <label>Remark</label>
                                                <textarea class="form-control" name="remark" id="remark" rows="4" placeholder=""></textarea>
                                            </div>



                                            <!-- Date range -->
                                            <div class="form-group ">
                                                <button type="submit" name="submit" value="submit" class="btn btn-success">Save</button>
                                            </div>
                                            <!-- /.form group -->
                                        </div>

                                        </form>
                                        <div class="card-footer">
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- // include footer -->
        <?php include  '../../include/footer.php'; ?>
    </div>
    <!-- ./wrapper -->
</body>

</html>

<!-- // ฟังก์ชันในการเพิ่มคอมม่าในตัวเลข -->
<script>
    function addCommas(nStr) {
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    function removeCommas(nStr) {
        return nStr.replace(/,/g, '');
    }

    document.addEventListener('DOMContentLoaded', function() {
        var priceInputs = document.querySelectorAll('input[type="int"]');

        priceInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                var cleanValue = removeCommas(this.value);
                if (!isNaN(cleanValue) && cleanValue.length > 0) {
                    this.value = addCommas(cleanValue);
                }
            });
        });

        document.querySelector('form').addEventListener('submit', function(event) {
            priceInputs.forEach(function(input) {
                input.value = removeCommas(input.value);
            });
        });
    });
</script>

<!-- คำนวณ Cost Project -->
<script>
    $(document).ready(function() {
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function calculateNoVatPrice(priceWithVat, vat) {
            return priceWithVat / (1 + (vat / 100));
        }

        function calculateWithVatPrice(priceNoVat, vat) {
            return priceNoVat * (1 + (vat / 100));
        }

        function calculateGrossProfit() {
            var saleNoVat = parseFloat($("#sale_no_vat").val().replace(/,/g, "")) || 0;
            var costNoVat = parseFloat($("#cost_no_vat").val().replace(/,/g, "")) || 0;

            if (saleNoVat && costNoVat) {
                var grossProfit = saleNoVat - costNoVat;
                $("#gross_profit").val(formatNumber(grossProfit.toFixed(2)));

                var grossProfitPercentage = (grossProfit / saleNoVat) * 100;
                $("#potential").val(grossProfitPercentage.toFixed(2) + "%");
            }
        }

        // ฟังก์ชันคำนวณ Estimate Potential
        function recalculateEstimate() {
            var saleNoVat = parseFloat($("#sale_no_vat").val().replace(/,/g, "")) || 0;
            var costNoVat = parseFloat($("#cost_no_vat").val().replace(/,/g, "")) || 0;
            var status = $("#status").val();
            var estimateSaleNoVat = 0;
            var estimateCostNoVat = 0;

            // กำหนดเปอร์เซ็นต์ตามสถานะ
            var percentage = 0;
            switch (status) {
                case 'Lost':
                    percentage = 0;
                    break;
                case 'Quotation':
                    percentage = 10;
                    break;
                case 'Negotiation':
                    percentage = 30;
                    break;
                case 'Bidding':
                    percentage = 50;
                    break;
                case 'Win':
                    percentage = 100;
                    break;
            }

            // คำนวณตามเปอร์เซ็นต์ที่กำหนด
            estimateSaleNoVat = (saleNoVat * percentage) / 100;
            estimateCostNoVat = (costNoVat * percentage) / 100;

            // แสดงผลการคำนวณ
            $("#es_sale_no_vat").val(formatNumber(estimateSaleNoVat.toFixed(2)));
            $("#es_cost_no_vat").val(formatNumber(estimateCostNoVat.toFixed(2)));
            $("#es_gp_no_vat").val(formatNumber((estimateSaleNoVat - estimateCostNoVat).toFixed(2)));
        }

        // คำนวณเมื่อกรอกข้อมูลในช่อง ราคาขาย/รวมภาษีมูลค่าเพิ่ม
        $("#sale_vat").on("input", function() {
            var saleVat = parseFloat($(this).val().replace(/,/g, "")) || 0;
            var vat = parseFloat($("#vat").val()) || 0;

            var saleNoVat = calculateNoVatPrice(saleVat, vat);
            $("#sale_no_vat").val(formatNumber(saleNoVat.toFixed(2)));

            calculateGrossProfit();
            recalculateEstimate(); // คำนวณ Estimate Potential
        });

        // คำนวณเมื่อกรอกข้อมูลในช่อง ราคาขาย/รวมไม่ภาษีมูลค่าเพิ่ม
        $("#sale_no_vat").on("input", function() {
            var saleNoVat = parseFloat($(this).val().replace(/,/g, "")) || 0;
            var vat = parseFloat($("#vat").val()) || 0;

            if (saleNoVat && vat) {
                var saleVat = calculateWithVatPrice(saleNoVat, vat);
                $("#sale_vat").val(formatNumber(saleVat.toFixed(2)));
            }

            calculateGrossProfit();
            recalculateEstimate(); // คำนวณ Estimate Potential
        });

        // คำนวณเมื่อกรอกข้อมูลในช่อง ราคาต้นทุน/รวมภาษีมูลค่าเพิ่ม
        $("#cost_vat").on("input", function() {
            var costVat = parseFloat($(this).val().replace(/,/g, "")) || 0;
            var vat = parseFloat($("#vat").val()) || 0;

            var costNoVat = calculateNoVatPrice(costVat, vat);
            $("#cost_no_vat").val(formatNumber(costNoVat.toFixed(2)));

            calculateGrossProfit();
            recalculateEstimate(); // คำนวณ Estimate Potential
        });

        // คำนวณเมื่อกรอกข้อมูลในช่อง ราคาต้นทุน/รวมไม่ภาษีมูลค่าเพิ่ม
        $("#cost_no_vat").on("input", function() {
            calculateGrossProfit();
            recalculateEstimate(); // คำนวณ Estimate Potential
        });

        // คำนวณเมื่อเปลี่ยนค่า VAT
        $("#vat").on("change", function() {
            $("#sale_vat").trigger("input");
            $("#sale_no_vat").trigger("input");
            $("#cost_vat").trigger("input");
            $("#cost_no_vat").trigger("input");
        });

        // คำนวณเมื่อเปลี่ยนสถานะ
        $("#status").on("change", function() {
            recalculateEstimate(); // คำนวณ Estimate Potential ใหม่
        });
    });
</script>