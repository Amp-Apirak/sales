<!-- Styled Modal สำหรับการชำระเงิน -->
<style>
    #paymentModal .modal-dialog {
        max-width: 600px;
    }

    #paymentModal .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    #paymentModal .modal-header {
        background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
        color: white;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        padding: 20px 25px;
        border: none;
    }

    #paymentModal .modal-title {
        font-weight: 600;
        font-size: 20px;
        display: flex;
        align-items: center;
    }

    #paymentModal .modal-title i {
        margin-right: 10px;
        font-size: 24px;
    }

    #paymentModal .close {
        color: white;
        opacity: 0.9;
        text-shadow: none;
        font-size: 28px;
    }

    #paymentModal .close:hover {
        opacity: 1;
    }

    #paymentModal .modal-body {
        padding: 30px 25px;
        background: #f8f9fa;
    }

    #paymentModal .form-group {
        margin-bottom: 20px;
    }

    #paymentModal .form-group label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 14px;
        display: flex;
        align-items: center;
    }

    #paymentModal .form-group label i {
        margin-right: 8px;
        color: #3498db;
        width: 18px;
    }

    #paymentModal .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }

    #paymentModal .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.15);
        background: white;
    }

    #paymentModal .form-control[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    #paymentModal .modal-footer {
        padding: 20px 25px;
        border-top: 1px solid #dee2e6;
        background: white;
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
    }

    #paymentModal .modal-footer .btn {
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
    }

    #paymentModal .modal-footer .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    #paymentModal .modal-footer .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    #paymentModal .modal-footer .btn-primary {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
    }

    #paymentModal .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #2980b9 0%, #21618c 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    /* Responsive */
    @media (max-width: 576px) {
        #paymentModal .modal-dialog {
            margin: 10px;
        }

        #paymentModal .modal-body {
            padding: 20px 15px;
        }

        #paymentModal .modal-header,
        #paymentModal .modal-footer {
            padding: 15px;
        }

        #paymentModal .modal-title {
            font-size: 18px;
        }
    }

    /* แถบแสดงสถานะ */
    #paymentModal #status {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%233498db' d='M10.293 3.293L6 7.586 1.707 3.293A1 1 0 00.293 4.707l5 5a1 1 0 001.414 0l5-5a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 12px;
        padding-right: 40px;
    }

    /* ข้อความช่วยเหลือ */
    #amountPaidHelp {
        margin-top: 8px;
        padding: 8px 12px;
        background-color: #f8f9fa;
        border-left: 3px solid #3498db;
        border-radius: 4px;
        font-size: 13px;
        line-height: 1.5;
    }

    #amountPaidHelp i {
        margin-right: 6px;
    }

    /* Animation สำหรับช่อง amountPaid */
    #amountPaid {
        transition: background-color 0.3s ease, border-color 0.3s ease;
        font-weight: 600;
        color: #2c3e50;
    }

    #amountPaid:focus {
        outline: none;
    }
</style>

<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="fas fa-money-check-alt"></i>
                    เพิ่มการชำระเงิน
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" id="paymentId">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="paymentNumber">
                                    <i class="fas fa-hashtag"></i>
                                    งวดที่
                                </label>
                                <input type="number" class="form-control" id="paymentNumber" required placeholder="ระบุงวดที่">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">
                                    <i class="fas fa-info-circle"></i>
                                    สถานะ
                                </label>
                                <select class="form-control" id="status" required>
                                    <option value="Pending">รอชำระ</option>
                                    <option value="Paid">ชำระแล้ว</option>
                                    <option value="Overdue">เกินกำหนด</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="paymentPercentage">
                                    <i class="fas fa-percentage"></i>
                                    เปอร์เซ็นต์การชำระ (%)
                                </label>
                                <input type="text" class="form-control" id="paymentPercentage" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">
                                    <i class="fas fa-coins"></i>
                                    จำนวนเงิน (บาท)
                                </label>
                                <input type="text" class="form-control" id="amount" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dueDate">
                                    <i class="far fa-calendar-alt"></i>
                                    วันครบกำหนด
                                </label>
                                <input type="date" class="form-control" id="dueDate">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="paymentDate">
                                    <i class="far fa-calendar-check"></i>
                                    วันที่ชำระ
                                </label>
                                <input type="date" class="form-control" id="paymentDate">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amountPaid">
                            <i class="fas fa-check-circle"></i>
                            จำนวนเงินที่ชำระแล้ว (บาท)
                        </label>
                        <input type="text" class="form-control" id="amountPaid" readonly placeholder="0.00">
                        <small class="form-text text-muted" id="amountPaidHelp">
                            <i class="fas fa-info-circle"></i> แสดงยอดรวมที่ชำระแล้วทั้งหมด (รวมงวดนี้ถ้าเลือกสถานะ "ชำระแล้ว")
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> ยกเลิก
                </button>
                <button type="button" class="btn btn-primary" onclick="savePayment()">
                    <i class="fas fa-save"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // ตัวแปรสำหรับเก็บข้อมูลการชำระเงินทั้งหมด
    let payments = <?php echo json_encode($payments); ?>;
    let totalSaleAmount = <?php echo $project['sale_no_vat']; ?>; // ราคาขาย (ไม่รวมภาษี)

    // ฟังก์ชันสำหรับฟอร์แมตตัวเลขให้มีคอมม่าและทศนิยม 2 ตำแหน่ง
    function formatNumber(num) {
        // แปลงเป็นตัวเลขก่อน (รองรับทั้ง string และ number)
        let value;
        if (typeof num === 'string') {
            value = parseFloat(num.replace(/,/g, ''));
        } else if (typeof num === 'number') {
            value = num;
        } else {
            return '0.00';
        }

        // ถ้าไม่ใช่ตัวเลข ให้คืนค่า 0.00
        if (isNaN(value)) {
            return '0.00';
        }

        return new Intl.NumberFormat('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }

    // ฟังก์ชันสำหรับแปลงข้อความที่มีคอมม่าเป็นตัวเลข
    function parseFormattedNumber(str) {
        // ถ้าเป็น number อยู่แล้ว ให้คืนค่าเลย
        if (typeof str === 'number') {
            return str;
        }
        // ถ้าไม่ใช่ string ให้คืนค่า 0
        if (typeof str !== 'string') {
            return 0;
        }
        // แปลง string เป็น number
        const value = parseFloat(str.replace(/,/g, ''));
        return isNaN(value) ? 0 : value;
    }

    // ฟังก์ชันสำหรับจัดการการป้อนข้อมูลในช่องตัวเลข
    function setupNumberInput(inputId) {
        const input = document.getElementById(inputId);
        let previousValue = '';

        input.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            let value = e.target.value.replace(/[^0-9.]/g, '');

            // จำกัดให้มีจุดทศนิยมได้เพียงจุดเดียว
            let parts = value.split('.');
            if (parts.length > 2) {
                parts = [parts[0], parts.slice(1).join('')];
                value = parts.join('.');
            }

            // จำกัดทศนิยมให้เหลือ 2 ตำแหน่ง
            if (parts.length > 1) {
                parts[1] = parts[1].slice(0, 2);
                value = parts.join('.');
            }

            // แปลงค่าเป็นตัวเลขและฟอร์แมตใหม่
            const formattedValue = value ? formatNumber(parseFloat(value)) : '';

            // คำนวณตำแหน่ง cursor ใหม่
            const addedCommas = (formattedValue.match(/,/g) || []).length - (previousValue.match(/,/g) || []).length;
            const newCursorPosition = cursorPosition + addedCommas;

            // อัปเดตค่าในช่องป้อนข้อมูล
            e.target.value = formattedValue;

            // ตั้งตำแหน่ง cursor ใหม่
            e.target.setSelectionRange(newCursorPosition, newCursorPosition);

            previousValue = formattedValue;

            // ทริกเกอร์การคำนวณที่เกี่ยวข้อง
            if (inputId === 'paymentPercentage') {
                calculateAmountFromPercentage();
            } else if (inputId === 'amount') {
                calculatePercentageFromAmount();
            }
        });
    }

    // ฟังก์ชันสำหรับคำนวณจำนวนเงินจากเปอร์เซ็นต์
    function calculateAmountFromPercentage() {
        const percentage = parseFloat($('#paymentPercentage').val().replace(/,/g, '')) || 0;
        const amount = (percentage / 100) * totalSaleAmount;
        $('#amount').val(formatNumber(amount.toFixed(2)));
    }


    // ฟังก์ชันสำหรับคำนวณเปอร์เซ็นต์จากจำนวนเงิน
    function calculatePercentageFromAmount() {
        const amount = parseFloat($('#amount').val().replace(/,/g, '')) || 0;
        const percentage = (amount / totalSaleAmount) * 100;
        $('#paymentPercentage').val(percentage.toFixed(2));
    }

    // ฟังก์ชันสำหรับคำนวณยอดรวมที่ชำระแล้วทั้งหมด (ไม่รวมรายการปัจจุบัน)
    function calculateTotalPaid(excludePaymentId = null) {
        let totalPaid = 0;
        payments.forEach(payment => {
            // นับเฉพาะรายการที่สถานะเป็น "Paid" และไม่ใช่รายการที่กำลังแก้ไข
            if (payment.payment_id !== excludePaymentId && payment.status === 'Paid') {
                // ใช้ amount (จำนวนเงินงวดนั้นๆ) ไม่ใช่ amount_paid (ที่อาจเป็นยอดรวมสะสม)
                totalPaid += parseFloat(payment.amount) || 0;
            }
        });
        return totalPaid;
    }

    // ฟังก์ชันสำหรับอัปเดตจำนวนเงินที่ชำระแล้วตามสถานะการชำระเงิน
    function updateAmountPaid() {
        const status = document.getElementById('status').value;
        const amount = parseFormattedNumber(document.getElementById('amount').value);
        const currentPaymentId = document.getElementById('paymentId').value;

        // คำนวณยอดรวมที่ชำระแล้วทั้งหมด (ไม่รวมรายการปัจจุบัน)
        const previousTotalPaid = calculateTotalPaid(currentPaymentId);

        let helpText = '';

        if (status === 'Paid') {
            // แสดงยอดรวมทั้งหมด = ยอดเก่า + ยอดปัจจุบัน
            const totalWithCurrent = previousTotalPaid + amount;
            document.getElementById('amountPaid').value = formatNumber(totalWithCurrent);

            // สร้างข้อความอธิบายที่ชัดเจน
            if (previousTotalPaid > 0) {
                helpText = `<i class="fas fa-calculator"></i> <strong>คำนวณ:</strong> ${formatNumber(previousTotalPaid)} (ชำระแล้ว) + ${formatNumber(amount)} (งวดนี้) = <strong class="text-success">${formatNumber(totalWithCurrent)} บาท</strong>`;
            } else {
                helpText = `<i class="fas fa-info-circle"></i> งวดนี้เป็นงวดแรกที่ชำระ = <strong class="text-success">${formatNumber(amount)} บาท</strong>`;
            }

            // เปลี่ยนสีของ input เป็นเขียวอ่อน
            $('#amountPaid').css('background-color', '#e8f5e9');
        } else if (status === 'Pending') {
            // ถ้าสถานะเป็น Pending แสดงเฉพาะยอดเก่า
            document.getElementById('amountPaid').value = formatNumber(previousTotalPaid);

            if (previousTotalPaid > 0) {
                helpText = `<i class="fas fa-info-circle"></i> ยอดที่ชำระแล้วก่อนหน้านี้ = <strong>${formatNumber(previousTotalPaid)} บาท</strong> (งวดนี้รอชำระ)`;
            } else {
                helpText = `<i class="fas fa-hourglass-half"></i> ยังไม่มีการชำระเงิน (งวดนี้รอชำระ)`;
            }

            $('#amountPaid').css('background-color', '#fff3cd');
        } else if (status === 'Overdue') {
            // ถ้าสถานะเป็น Overdue
            document.getElementById('amountPaid').value = formatNumber(previousTotalPaid);

            if (previousTotalPaid > 0) {
                helpText = `<i class="fas fa-exclamation-triangle"></i> ยอดที่ชำระแล้ว = <strong>${formatNumber(previousTotalPaid)} บาท</strong> (งวดนี้เกินกำหนด)`;
            } else {
                helpText = `<i class="fas fa-exclamation-triangle text-danger"></i> <strong class="text-danger">ยังไม่มีการชำระเงิน (งวดนี้เกินกำหนด)</strong>`;
            }

            $('#amountPaid').css('background-color', '#f8d7da');
        }

        // อัพเดตข้อความช่วยเหลือ
        $('#amountPaidHelp').html(helpText);
    }

    // ฟังก์ชันเปิด Modal สำหรับเพิ่มการชำระเงิน
    function openAddPaymentModal() {
        document.getElementById('paymentModalLabel').innerHTML = '<i class="fas fa-money-check-alt"></i> เพิ่มการชำระเงิน';
        document.getElementById('paymentForm').reset();
        document.getElementById('paymentId').value = '';
        document.getElementById('paymentNumber').value = payments.length + 1;

        // แสดงยอดรวมที่ชำระแล้วทั้งหมด
        const totalPaid = calculateTotalPaid();
        document.getElementById('amountPaid').value = formatNumber(totalPaid);

        // ตั้งค่าเริ่มต้นให้แสดงข้อความช่วยเหลือ
        let helpText = '';
        if (totalPaid > 0) {
            helpText = `<i class="fas fa-info-circle"></i> ยอดที่ชำระแล้วก่อนหน้านี้ทั้งหมด = <strong>${formatNumber(totalPaid)} บาท</strong>`;
            $('#amountPaid').css('background-color', '#fff3cd');
        } else {
            helpText = `<i class="fas fa-info-circle"></i> ยังไม่มีการชำระเงินก่อนหน้านี้`;
            $('#amountPaid').css('background-color', '#e9ecef');
        }
        $('#amountPaidHelp').html(helpText);

        $('#paymentModal').modal('show');
    }

    // ฟังก์ชันเปิด Modal สำหรับแก้ไขการชำระเงิน
    function editPayment(paymentId) {
        const payment = payments.find(p => p.payment_id === paymentId);
        if (payment) {
            document.getElementById('paymentModalLabel').textContent = 'แก้ไขการชำระเงิน';
            document.getElementById('paymentId').value = payment.payment_id;
            document.getElementById('paymentNumber').value = payment.payment_number;
            document.getElementById('paymentPercentage').value = formatNumber(payment.payment_percentage);
            document.getElementById('amount').value = formatNumber(payment.amount);
            document.getElementById('dueDate').value = payment.due_date;
            document.getElementById('status').value = payment.status;
            document.getElementById('paymentDate').value = payment.payment_date || '';
            document.getElementById('amountPaid').value = formatNumber(payment.amount_paid);
            $('#paymentModal').modal('show');
        }
    }

    // ฟังก์ชันสำหรับบันทึกข้อมูลการชำระเงิน (เพิ่มหรือแก้ไข)
    function savePayment() {
        const paymentData = {
            csrf_token: document.querySelector('input[name="csrf_token"]').value,
            payment_id: document.getElementById('paymentId').value,
            project_id: '<?php echo $project_id; ?>',
            payment_number: document.getElementById('paymentNumber').value,
            amount: parseFormattedNumber(document.getElementById('amount').value),
            payment_percentage: parseFormattedNumber(document.getElementById('paymentPercentage').value),
            due_date: document.getElementById('dueDate').value,
            status: document.getElementById('status').value,
            payment_date: document.getElementById('paymentDate').value,
            amount_paid: parseFormattedNumber(document.getElementById('amountPaid').value)
        };

        // คำนวณเปอร์เซ็นต์รวมของการชำระเงินทั้งหมด
        let totalPercentage = payments.reduce((total, payment) => {
            // ถ้ากำลังแก้ไขรายการปัจจุบัน ไม่นับเปอร์เซ็นต์เดิม
            if (payment.payment_id !== paymentData.payment_id) {
                return total + parseFloat(payment.payment_percentage);
            }
            return total;
        }, 0);

        // เพิ่มเปอร์เซ็นต์ของการชำระเงินปัจจุบัน
        totalPercentage += parseFloat(paymentData.payment_percentage);

        if (totalPercentage > 100) {
            Swal.fire({
                icon: 'warning',
                title: 'เกินขีดจำกัด',
                text: 'เปอร์เซ็นต์รวมของการชำระเงินเกิน 100% ของราคาขาย (ไม่รวมภาษี)',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        $.ajax({
            url: 'save_payment.php',
            type: 'POST',
            data: paymentData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'บันทึกข้อมูลสำเร็จ',
                        confirmButtonText: 'ตกลง'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message,
                        confirmButtonText: 'ตกลง'
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + textStatus,
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    }

    // ฟังก์ชันสำหรับลบข้อมูลการชำระเงิน
    function deletePayment(paymentId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบรายการชำระเงินนี้ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_payment.php',
                    type: 'POST',
                    data: {
                        csrf_token: document.querySelector('input[name="csrf_token"]').value,
                        payment_id: paymentId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: response.message || 'ลบข้อมูลสำเร็จ',
                                confirmButtonText: 'ตกลง'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: (response && response.message) ? response.message : 'ไม่สามารถลบข้อมูลได้',
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error:', textStatus, errorThrown);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + textStatus,
                            confirmButtonText: 'ตกลง'
                        });
                    }
                });
            }
        });
    }

    // เพิ่ม Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        setupNumberInput('paymentPercentage');
        setupNumberInput('amount');
        document.getElementById('status').addEventListener('change', updateAmountPaid);
        // ฟังก์ชันจัดการการป้อนข้อมูลเปอร์เซ็นต์
        document.getElementById('paymentPercentage').addEventListener('input', calculateAmountFromPercentage);
        // ฟังก์ชันจัดการการป้อนจำนวนเงิน  
        document.getElementById('amount').addEventListener('input', calculatePercentageFromAmount);
    });

    // Event Listeners
    $('#paymentPercentage').on('input', function() {
        calculateAmountFromPercentage();
        updateAmountPaid(); // อัพเดตยอดรวมเมื่อเปลี่ยนเปอร์เซ็นต์
    });

    $('#amount').on('input', function() {
        calculatePercentageFromAmount();
        updateAmountPaid(); // อัพเดตยอดรวมเมื่อเปลี่ยนจำนวนเงิน
    });

    $('#status').on('change', function() {
        updateAmountPaid(); // อัพเดตยอดรวมเมื่อเปลี่ยนสถานะ
    });
</script>