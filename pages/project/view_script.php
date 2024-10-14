<script>
    // ตัวแปรสำหรับเก็บข้อมูลการชำระเงินทั้งหมด
    let payments = <?php echo json_encode($payments); ?>;
    let totalSaleAmount = <?php echo $project['sale_vat']; ?>; // ราคาขาย (รวมภาษี)

    // ฟังก์ชันสำหรับฟอร์แมตตัวเลขให้มีคอมม่าและทศนิยม 2 ตำแหน่ง
    function formatNumber(num) {
        return parseInt(num).toLocaleString('th-TH');
    }

    // ฟังก์ชันสำหรับแปลงข้อความที่มีคอมม่าเป็นตัวเลข
    function parseFormattedNumber(str) {
        return parseFloat(str.replace(/,/g, '')) || 0;
    }
    // ฟังก์ชันสำหรับคำนวณจำนวนเงินจากเปอร์เซ็นต์
    function calculateAmountFromPercentage() {
        const percentage = parseFloat(document.getElementById('paymentPercentage').value) || 0;
        const amount = (percentage / 100) * totalSaleAmount;
        document.getElementById('amount').value = formatNumber(amount);
        updateAmountPaid();
    }


    // ฟังก์ชันสำหรับคำนวณเปอร์เซ็นต์จากจำนวนเงิน
    function calculatePercentageFromAmount() {
        const amount = parseFormattedNumber(document.getElementById('amount').value);
        const percentage = (amount / totalSaleAmount) * 100;
        document.getElementById('paymentPercentage').value = percentage.toFixed(2);
        updateAmountPaid();
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
            document.getElementById('paymentPercentage').value = ((payment.amount / totalSaleAmount) * 100).toFixed(2);
            document.getElementById('amount').value = formatNumber(payment.amount);
            document.getElementById('dueDate').value = payment.due_date;
            document.getElementById('status').value = payment.status;
            document.getElementById('paymentDate').value = payment.payment_date || '';
            document.getElementById('amountPaid').value = formatNumber(payment.amount_paid);
            $('#paymentModal').modal('show');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'ไม่พบข้อมูล',
                text: 'ไม่พบข้อมูลการชำระเงินที่ต้องการแก้ไข',
                confirmButtonText: 'ตกลง'
            });
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
            payment_percentage: parseFloat(document.getElementById('paymentPercentage').value),
            due_date: document.getElementById('dueDate').value,
            status: document.getElementById('status').value,
            payment_date: document.getElementById('paymentDate').value,
            amount_paid: parseFormattedNumber(document.getElementById('amountPaid').value)
        };

        // คำนวณเปอร์เซ็นต์รวมของการชำระเงินทั้งหมด
        let totalPercentage = payments.reduce((total, payment) => {
            return total + parseFloat(payment.payment_percentage);
        }, 0);

        // เพิ่มเปอร์เซ็นต์ของการชำระเงินใหม่
        totalPercentage += parseFloat(paymentData.payment_percentage);

        if (totalPercentage > 100) {
            Swal.fire({
                icon: 'warning',
                title: 'เกินขีดจำกัด',
                text: 'เปอร์เซ็นต์รวมของการชำระเงินเกิน 100% ของราคาขาย',
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
                        console.log('Raw response:', response);
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
                        console.log('Response Text:', jqXHR.responseText);
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
    document.getElementById('paymentPercentage').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9.]/g, '');
        e.target.value = value;
        calculateAmountFromPercentage();
    });

    document.getElementById('amount').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9.]/g, '');
        e.target.value = formatNumber(value);
        calculatePercentageFromAmount();
    });

    document.getElementById('status').addEventListener('change', updateAmountPaid);
</script>