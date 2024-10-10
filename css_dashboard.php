<style>
        .small-box {
            border-radius: 0.25rem;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
            display: block;
            margin-bottom: 20px;
            position: relative;
        }

        .small-box>.inner {
            padding: 10px;
        }

        .small-box h4 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 10px 0;
            padding: 0;
            white-space: nowrap;
        }

        .small-box p {
            font-size: 1rem;
        }

        .small-box .icon {
            color: rgba(0, 0, 0, .15);
            z-index: 0;
        }

        .small-box .icon>i {
            font-size: 70px;
            position: absolute;
            right: 15px;
            top: 15px;
            transition: transform .3s linear;
        }

        .small-box:hover .icon>i {
            transform: scale(1.1);
        }

        @media (max-width: 767.98px) {
            .small-box {
                text-align: center;
            }

            .small-box .icon {
                display: none;
            }

            .small-box p {
                font-size: 12px;
            }
        }

        .card-body {
            padding: 0.5rem;
        }

        .form-label {
            margin-bottom: 0;
            font-size: 0.875rem;
        }

        .form-select-sm,
        .form-control-sm {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        @media (max-width: 767.98px) {
            .row>div {
                margin-bottom: 0.5rem;
            }
        }

        .card-statistic {
            border: none;
            box-shadow: 0 0 35px 0 rgba(154, 161, 171, .15);
            transition: all 0.3s ease-in-out;
        }

        .card-statistic:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-statistic .card-body {
            padding: 1.5rem;
        }

        .icon-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-circle i {
            font-size: 1.5rem;
            color: #fff;
        }

        .card-statistic h2 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .card-statistic p {
            font-size: 0.9rem;
        }

        .bg-primary {
            background: linear-gradient(45deg, #4099ff, #73b4ff);
        }

        .bg-success {
            background: linear-gradient(45deg, #2ed8b6, #59e0c5);
        }

        .bg-danger {
            background: linear-gradient(45deg, #ff5370, #ff869a);
        }

        .bg-warning {
            background: linear-gradient(45deg, #ffb64d, #ffcb80);
        }

        .card-primary.card-outline {
            border-top: 3px solid #007bff;
        }

        .card-success.card-outline {
            border-top: 3px solid #28a745;
        }

        .card-header {
            background-color: rgba(0, 0, 0, .03);
            border-bottom: 1px solid rgba(0, 0, 0, .125);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 400;
            margin: 0;
        }

        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
        }
    </style>
