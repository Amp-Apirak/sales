<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">เพิ่มการชำระเงิน</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" id="paymentId">
                    <div class="form-group">
                        <label for="paymentNumber">งวดที่</label>
                        <input type="number" class="form-control" id="paymentNumber" required>
                    </div>
                    <div class="form-group">
                        <label for="paymentPercentage">เปอร์เซ็นต์การชำระ (%)</label>
                        <input type="text" class="form-control" id="paymentPercentage" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="amount">จำนวนเงิน (บาท)</label>
                        <input type="text" class="form-control" id="amount">
                    </div>
                    <div class="form-group">
                        <label for="dueDate">วันครบกำหนด</label>
                        <input type="date" class="form-control" id="dueDate">
                    </div>
                    <div class="form-group">
                        <label for="status">สถานะ</label>
                        <select class="form-control" id="status" required>
                            <option value="Pending">รอชำระ</option>
                            <option value="Paid">ชำระแล้ว</option>
                            <option value="Overdue">เกินกำหนด</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="paymentDate">วันที่ชำระ</label>
                        <input type="date" class="form-control" id="paymentDate">
                    </div>
                    <div class="form-group">
                        <label for="amountPaid">จำนวนเงินที่ชำระแล้ว (บาท)</label>
                        <input type="text" class="form-control" id="amountPaid" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="savePayment()">บันทึก</button>
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
        return new Intl.NumberFormat('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(num);
    }

    // ฟังก์ชันสำหรับแปลงข้อความที่มีคอมม่าเป็นตัวเลข
    function parseFormattedNumber(str) {
        return parseFloat(str.replace(/,/g, '')) || 0;
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

    // ฟังก์ชันสำหรับอัปเดตจำนวนเงินที่ชำระแล้วตามสถานะการชำระเงิน
    function updateAmountPaid() {
        const status = document.getElementById('status').value;
        const amount = parseFormattedNumber(document.getElementById('amount').value);
        if (status === 'Paid') {
            document.getElementById('amountPaid').value = formatNumber(amount);
        } else {
            document.getElementById('amountPaid').value = formatNumber(0);
        }
    }

    // ฟังก์ชันเปิด Modal สำหรับเพิ่มการชำระเงิน
    function openAddPaymentModal() {
        document.getElementById('paymentModalLabel').textContent = 'เพิ่มการชำระเงิน';
        document.getElementById('paymentForm').reset();
        document.getElementById('paymentId').value = '';
        document.getElementById('paymentNumber').value = payments.length + 1;
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
    });

    $('#amount').on('input', function() {
        calculatePercentageFromAmount();
    });
</script>