<!DOCTYPE html>
<html lang='en'>

<head>
  <meta charset='utf-8'>
  <title><?php echo $email_title; ?></title>

  <style>
        .email-body {
            margin: 0;
            background-color: #fff;
        }

        .container {
            width: 50%;
            margin: auto;
            margin-top: 60px;
            border: 1px solid green;
        }

        .email-header {
            color: #fff;
            padding: 10px 5px;
            text-align: center;
            background-color: green;
        }

        .email-header h1 {
            margin: 0;
            text-transform: lowercase;
            font-variant: small-caps;
        }

        .main {
            padding: 5px;
        }

        .btn {
            color: #fff;
            margin: 5px 0;
            padding: 10px;
            display: block;
            text-align: center;
            border-radius: 2px;
            border-color: #46b8da;
            text-decoration: none;
            box-sizing: border-box;
            font-variant: small-caps;
            background-color: #5bc0de;
        }

        .email-footer {
            color: green;
            padding: 10px 5px;
            text-align: center;
            border-top: 1px solid green;
        }

        @media only screen and (max-width: 767px) {
            .email-body {
                font-size: 14px;
            }
            .container {
                width: 90%;
                margin-top: 15px;
            }
            .email-header h1 {
                font-size: 18px;
            }
        }
  </style>
</head>

  <body class='email-body'>
    <div class='container'>
      <div class='email-header'>
        <h1><?php echo $email_heading; ?></h1>
      </div>
      <div class='main' role='main'>
        <?php echo $message; ?>
      </div>
      <div class='email-footer'>
        &copy; <?php echo date('Y'); ?> Makwire
      </div>
    </div>
  </body>
</html>
