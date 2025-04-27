<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Invoice Print</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../assets/css/adminlte.min.css">
</head>

<body>
    <div class="wrapper">
        <!-- Main content -->
        <section class="invoice">
            <!-- title row -->
            <div class="row">
                <div class="col-12">
                    <h5 class="page-header">
                        <img src="../../assets/img/pit.png" alt="Point IT Logo" style="height: 60px; margin-right: 10px;"> Point IT
                        <small class="float-right">Date: 2/10/2014(Today)</small>
                    </h5>
                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    From
                    <address>
                        <strong>Saller:</strong><br>
                        Company:<br>
                        Address:<br>
                        Phone: <br>
                        Email:
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    To
                    <address>
                        <strong>Customer name:</strong><br>
                        Company:<br>
                        Address:<br>
                        Phone: <br>
                        Email:
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    <b>Invoice #007612</b><br>
                    <br>
                    <b>Order ID:</b> 4F3S8J<br>
                    <b>Payment Due:</b> 2/22/2014<br>
                    <b>Account:</b> 968-34567
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Type</th>
                                <th>Part No.</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Total Amont</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>A</td>
                                <td>Service</td>
                                <td>El snort testosterone trophy driving gloves handsome</td>
                                <td>3</td>
                                <td>คน</td>
                                <td>$150.00</td>
                                <td>$450.00</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>B</td>
                                <td>Service</td>
                                <td>El snort testosterone trophy driving gloves handsome</td>
                                <td>3</td>
                                <td>ชิ้น</td>
                                <td>$150.00</td>
                                <td>$450.00</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>C</td>
                                <td>Service</td>
                                <td>El snort testosterone trophy driving gloves handsome</td>
                                <td>3</td>
                                <td>ชิ้น</td>
                                <td>$150.00</td>
                                <td>$450.00</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>D</td>
                                <td>Service</td>
                                <td>El snort testosterone trophy driving gloves handsome</td>
                                <td>3</td>
                                <td>ตัว</td>
                                <td>$150.00</td>
                                <td>$450.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row">
                <!-- accepted payments column -->
                <div class="col-6">
                    <p class="lead">Payment Methods:</p>
                    <img src="../../assets/dist/img/credit/visa.png" alt="Visa">
                    <img src="../../assets/dist/img/credit/mastercard.png" alt="Mastercard">
                    <img src="../../assets/dist/img/credit/american-express.png" alt="American Express">
                    <img src="../../assets/dist/img/credit/paypal2.png" alt="Paypal">

                    <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem plugg dopplr
                        jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
                    </p>
                </div>
                <!-- /.col -->
                <div class="col-6">
                    <p class="lead">Amount Due 2/22/2014</p>

                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th style="width:50%">Total Amount:</th>
                                <td>$250.30</td>
                            </tr>
                            <tr>
                                <th>Vat (7%):</th>
                                <td>$10.34</td>
                            </tr>
                            <tr>
                                <th>Grand Total:</th>
                                <td>$265.24</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- ./wrapper -->
    <!-- Page specific script -->
    <script>
        window.addEventListener("load", window.print());
    </script>
</body>

</html>