<!-- สไตล์ CSS สำหรับหน้าเว็บ -->
<style>
    /* ใช้ฟอนต์ Noto Sans Thai กับทุกอีลีเมนต์ */
    body,
    html {
        font-family: 'Noto Sans Thai', sans-serif;
    }

    /* ปรับแต่งสไตล์เฉพาะสำหรับหัวข้อและตาราง */
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-weight: 700;
    }

    th {
        font-weight: 600;
    }

    .custom-th {
        font-weight: 600;
        font-size: 18px;
        color: #FF5733;
    }

    /* สไตล์สำหรับส่วนหัวของ Category */
    .category-header {
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        color: white;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    /* สไตล์สำหรับ info box */
    .info-box {
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        border-radius: 0.25rem;
        background-color: #ffffff;
        display: flex;
        margin-bottom: 1rem;
        min-height: 80px;
        padding: .5rem;
        position: relative;
        width: 100%;
    }

    .info-box .info-box-icon {
        border-radius: 0.25rem;
        align-items: center;
        display: flex;
        font-size: 1.875rem;
        justify-content: center;
        text-align: center;
        width: 70px;
    }

    .info-box .info-box-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.8;
        flex: 1;
        padding: 0 10px;
    }

    .info-box .info-box-number {
        display: block;
        margin-top: .25rem;
        font-weight: 700;
    }

    /* สไตล์สำหรับเนื้อหา Category */
    .category-content {
        background-color: #f4f6f9;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }

    /* สไตล์สำหรับแกลเลอรีรูปภาพ */
    .image-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 20px;
    }

    .image-item {
        position: relative;
        width: 200px;
    }

    .image-item img {
        width: 100%;
        height: auto;
        border-radius: 5px;
    }

    .image-actions {
        position: absolute;
        top: 5px;
        right: 5px;
        display: flex;
        gap: 5px;
    }

    .image-actions button {
        padding: 5px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    /* สไตล์สำหรับปุ่ม */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }

    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
        box-shadow: 0 2px 2px 0 rgba(0, 123, 255, .14), 0 3px 1px -2px rgba(0, 123, 255, .2), 0 1px 5px 0 rgba(0, 123, 255, .12);
        transition: all 0.2s ease-in-out;
    }

    .btn-primary:hover {
        color: #fff;
        background-color: #0069d9;
        border-color: #0062cc;
        box-shadow: 0 4px 5px 0 rgba(0, 123, 255, .14), 0 1px 10px 0 rgba(0, 123, 255, .12), 0 2px 4px -1px rgba(0, 123, 255, .2);
    }

    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
        box-shadow: 0 2px 2px 0 rgba(108, 117, 125, .14), 0 3px 1px -2px rgba(108, 117, 125, .2), 0 1px 5px 0 rgba(108, 117, 125, .12);
        transition: all 0.2s ease-in-out;
    }

    .btn-secondary:hover {
        color: #fff;
        background-color: #5a6268;
        border-color: #545b62;
        box-shadow: 0 4px 5px 0 rgba(108, 117, 125, .14), 0 1px 10px 0 rgba(108, 117, 125, .12), 0 2px 4px -1px rgba(108, 117, 125, .2);
    }

    /* สไตล์สำหรับ content wrapper */
    .content-wrapper {
        min-height: calc(100vh - 150px);
        padding-bottom: 60px;
    }

    /* สไตล์สำหรับรายละเอียด Category */
    .category-details {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }

    .detail-card {
        flex: 1 1 calc(33.333% - 20px);
        min-width: 250px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
    }

    .detail-card-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
        font-weight: bold;
    }

    .detail-card-body {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .detail-card-content {
        flex-grow: 1;
        min-height: 100px;
    }

    /* สไตล์สำหรับรูปภาพ Category */
    .category-images {
        margin-top: 20px;
    }

    .btn-add-image {
        float: right;
    }

    /* สไตล์สำหรับการ์ดข้อมูล */
    .info-cards {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .info-card {
        flex: 1;
        min-width: 200px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
    }

    .info-card-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    .info-card-content {
        flex-grow: 1;
        padding: 15px;
    }

    .info-card-label {
        font-size: 14px;
        color: #666;
        margin-bottom: 5px;
    }

    .info-card-value {
        font-size: 16px;
        font-weight: bold;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<style>
    /* สไตล์สำหรับ content wrapper */
    .content-wrapper {
        background-color: #f4f6f9;
    }

    /* สไตล์สำหรับ card */
    .card {
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        margin-bottom: 1rem;
    }

    /* สไตล์สำหรับ card header */
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, .125);
        padding: .75rem 1.25rem;
        position: relative;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
    }

    /* สไตล์สำหรับ card title */
    .card-title {
        color: white;
        margin-bottom: 0;
    }

    /* สไตล์สำหรับ label ในฟอร์ม */
    .form-group label {
        font-weight: 700;
    }

    /* สไตล์สำหรับ input fields */
    .form-control {
        border-radius: .25rem;
    }

    /* สไตล์สำหรับปุ่ม Save */
    .btn-success {
        color: #fff;
        background-color: #28a745;
        border-color: #28a745;
        box-shadow: none;
    }

    /* สไตล์สำหรับปุ่มแบบ block */
    .btn-block {
        display: block;
        width: 100%;
    }
</style>