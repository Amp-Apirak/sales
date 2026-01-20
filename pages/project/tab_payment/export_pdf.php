<!-- 5.1  เพิ่ม CSS สำหรับ loading indicator -->
<style>
    #loading {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        text-align: center;
        color: white;
        padding-top: 200px;
    }
</style>
<!-- 5.1  เพิ่ม CSS สำหรับ loading indicator -->

<!-- 5.2 เพิ่ม loading indicator -->
<div id="loading">
    <h3>กำลังสร้าง PDF...</h3>
</div>
<!-- 5.2 เพิ่ม loading indicator -->

<!-- 5.3 ปรับปรุงฟังก์ชัน generatePDF -->
<script>
    function generatePDF() {

        // ซ่อนปุ่มก่อนสร้าง PDF
        const buttons = document.querySelectorAll('.edit-button, .btn-sm');
        buttons.forEach(button => button.style.display = 'none');

        // แสดง loading indicator
        document.getElementById('loading').style.display = 'block';

        // เลือกเฉพาะส่วนที่ต้องการพิมพ์
        const element = document.getElementById('project-info');

        // คลี่ตารางที่ซ่อนอยู่ใน responsive container
        const responsiveTables = element.querySelectorAll('.table-responsive');
        responsiveTables.forEach(container => {
            container.style.overflow = 'visible';
            container.style.maxWidth = 'none';
        });

        // กำหนดตัวเลือกสำหรับ html2pdf
        const opt = {
            margin: [20, 10, 10, 10],
            filename: 'project-details.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                windowWidth: 1200 // กำหนดความกว้างของ viewport
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            },
            pagebreak: {
                mode: ['avoid-all', 'css', 'legacy']
            }
        };

        // สร้าง PDF
        html2pdf().from(element).set(opt).save().then(() => {
            // คืนค่าสไตล์เดิมให้กับตาราง
            responsiveTables.forEach(container => {
                container.style.overflow = '';
                container.style.maxWidth = '';
            });
            // แสดงปุ่มกลับมาหลังสร้าง PDF เสร็จ
            buttons.forEach(button => button.style.display = '');
            document.getElementById('loading').style.display = 'none';
        });
    }
</script>

<!-- 5.3 ปรับปรุงฟังก์ชัน generatePDF -->
<style>
    /* CSS สำหรับการพิมพ์ PDF */
    @media print {

        /* การตั้งค่าพื้นฐานสำหรับ body และ container */
        body {
            font-size: 12pt;
            /* กำหนดขนาดฟอนต์ให้เหมาะสม */
            padding: 0;
            margin: 0;
        }

        .container-fluid {
            width: 100%;
            /* กำหนดให้ container กินพื้นที่เต็มหน้า */
            padding: 0;
            margin: 0;
        }

        .wrapper,
        .content-wrapper {
            background-color: white !important;
            /* กำหนดสีพื้นหลังเป็นสีขาว */
        }

        /* ซ่อนองค์ประกอบที่ไม่ต้องการให้พิมพ์ */
        .nav-pills,
        .card-header p-2,
        .nav.nav-pills,
        .tab-content>.tab-pane:not(.active),
        .nav-item,
        .edit-button,
        .btn-sm,
        .btn-info,
        .btn-danger,
        .btn-group,
        .no-print,
        .main-sidebar,
        .main-header,
        .main-footer {
            display: none !important;
            /* ไม่แสดงปุ่มหรือส่วนที่ไม่จำเป็น */
        }


        /* การจัดรูปแบบของ info-card และ row */
        .info-card {
            page-break-inside: avoid;
            /* ป้องกันไม่ให้ตัดหน้าในขณะพิมพ์ */
            margin-bottom: 20px;
            width: 100%;
            break-inside: avoid;
            /* ป้องกันการตัดหน้า */
        }

        .row {
            display: block;
            page-break-inside: avoid;
            /* ป้องกันการตัดหน้าในขณะพิมพ์ */
            margin-bottom: 20px;
        }

        /* การจัดการโครงสร้างของคอลัมน์และเนื้อหา info-card */
        .col-md-12 {
            width: 100%;
            /* ปรับให้คอลัมน์มีขนาดเต็ม */
            float: none;
        }

        .info-card-body {
            padding: 15px;
            /* กำหนดระยะห่างภายในของการ์ด */
        }

        .info-item {
            margin-bottom: 10px;
            /* กำหนดระยะห่างระหว่างแต่ละรายการ */
        }

        .info-label {
            font-weight: bold;
            /* กำหนดตัวหนาให้กับ label */
            display: inline-block;
            width: 150px;
            /* กำหนดความกว้างของ label */
            vertical-align: top;
        }

        .info-value {
            display: inline-block;
            width: calc(100% - 160px);
            /* กำหนดให้แสดงผลเต็มพื้นที่ */
        }

        /* การจัดการตาราง */
        .table-responsive {
            overflow-x: visible !important;
            /* แก้ไขปัญหาการแสดงผลของตารางใน container */
        }

        .table {
            width: 100% !important;
            /* กำหนดให้ตารางเต็มหน้ากระดาษ */
            table-layout: fixed;
            /* ใช้การจัดรูปแบบตารางให้เท่ากันทุกคอลัมน์ */
            border-collapse: collapse !important;
            /* รวมเส้นขอบของตาราง */
        }

        .table-section {
            page-break-inside: avoid;
            /* ป้องกันไม่ให้ตัดหน้าในขณะพิมพ์ */
        }

        .table th,
        .table td {
            word-wrap: break-word;
            /* แก้ไขปัญหาคำใน cell ยาวเกิน */
            max-width: 100%;
            white-space: normal;
            background-color: #fff !important;
            /* กำหนดสีพื้นหลังของ cell ให้เป็นสีขาว */
        }

        /* การจัดการหัวข้อและรูปภาพ */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            page-break-after: avoid;
            /* ป้องกันการตัดหน้าในหัวข้อ */
        }

        img {
            max-width: 100% !important;
            /* ป้องกันไม่ให้ภาพขยายเกินขอบ */
        }
    }

    /* การปรับแต่งตารางต้นทุนโครงการ (Datatables) */
    .table-responsive {
        margin: 15px 0;
        /* กำหนดระยะห่างรอบตาราง */
    }

    #costTable {
        width: 100% !important;
        /* กำหนดความกว้างของตารางให้เต็ม */
        margin-bottom: 1rem;
    }

    #costTable th,
    #costTable td {
        padding: 8px;
        /* กำหนดระยะห่างภายใน cell */
        vertical-align: middle;
        /* จัดแนวข้อมูลให้อยู่กลาง */
    }

    .dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
        padding: 0 15px;
        /* จัดระยะห่างภายใน wrapper */
    }

    /* สไตล์ปุ่ม DataTables */
    .dt-buttons {
        margin-bottom: 15px;
        float: left;
        /* จัดตำแหน่งปุ่มไปทางซ้าย */
    }

    .dt-button {
        margin-right: 5px !important;
        /* ระยะห่างระหว่างปุ่ม */
    }

    /* การปรับปรุง responsive */
    @media screen and (max-width: 767px) {
        .table-responsive {
            border: none;
            /* ซ่อนเส้นขอบเมื่อลดขนาดจอ */
        }

        .dataTables_wrapper {
            padding: 0;
            /* ลบระยะห่างภายในเมื่อจอเล็ก */
        }
    }

    /* ปรับแต่งปุ่ม Export */
    .buttons-excel {
        color: #fff !important;
        background-color: #28a745 !important;
        /* กำหนดสีพื้นหลังเป็นสีเขียว */
        border-color: #28a745 !important;
        padding: .25rem .5rem !important;
        /* กำหนดระยะห่างภายในปุ่ม */
        font-size: .875rem !important;
        line-height: 1.5 !important;
        border-radius: .2rem !important;
        /* กำหนดขอบโค้งของปุ่ม */
    }
</style>
<!-- 5. function สำหรับการพิมพ์ PDF -->