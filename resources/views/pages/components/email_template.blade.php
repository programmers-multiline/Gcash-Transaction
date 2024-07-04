<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <title>MR Request</title> --}} 
</head>
<style>
    a:hover {text-decoration: underline !important;}
</style>
<body>

</body>

<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
        style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: Open Sans, sans-serif;">
        <tr>
            <td>
                <table style="background-color: #f2f3f8; max-width:670px; margin:0 auto;" width="100%" border="0"
                    align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="height:80px;">&nbsp;</td>
                    </tr>
                    <!-- Logo -->
                
                    
                    <!-- Email Content -->
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                style="max-width:670px; background:#fff; border-radius:3px;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);padding:0 40px;">
                                <tr>
                                    <td style="text-align:center;">
                                      <br>
                                      <img width="330px" src="https://multi-linegroupofcompanies.com/iconlogo/ml.png" title="logo" alt="logo">
                                    </td>
                                </tr>
                                <!-- Title -->
                                <tr>
                                    <td style="padding:0 15px; text-align:center;">
                                        <h2 style="color:#1e1e2d; font-weight:400; margin:0;font-size:32px;font-family:Rubik,sans-serif;text-align:center;">Tools and Equipment Monitoring System</h2>
                                        <span style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; 
                                        width:100px;"></span>
                                    </td>
                                </tr>
                                <!-- Details Table -->
                                <tr>
                                    <td>
                                        <h4 style="margin-top: 15px;">You have items to approve, Please review the details and provide your approval at your earliest convenience.</h4>
                                        <table cellpadding="0" cellspacing="0"
                                            style="width: 100%; border: 1px solid #ededed">
                                            <tbody>

                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)">
                                                        Requestor Name:</td>
                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #455056;">{{$mail_data['requestor_name']}}</td>
                                                </tr>


                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)">
                                                        Approver Name:</td>
                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #455056;">{{$mail_data['approver']}}</td>
                                                </tr>
                                            
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)">
                                                         Date Requested:</td>
                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #455056;">{{$mail_data['date_requested']}}</td>
                                                </tr>

                                            </tbody>
                                        </table>

                                        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; ">
                                            <thead>
                                                <tr>
                                                <th style="border: 1px solid #ededed; text-align: left; padding: 8px;" scope="col">Item Code</th>
                                                <th style="border: 1px solid #ededed; text-align: left; padding: 8px;" scope="col">Item Descriptiom</th>
                                                <th style="border: 1px solid #ededed; text-align: left; padding: 8px;" scope="col">Brand</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (json_decode($mail_data['items']) as $tool)
                                                <tr>
                                                    <td style="border: 1px solid #ededed; text-align: left; padding: 8px;">{{$tool->item_code}}</td>
                                                    <td style="border: 1px solid #ededed; text-align: left; padding: 8px;">{{$tool->item_description}}</td>
                                                    <td style="border: 1px solid #ededed; text-align: left; padding: 8px;">{{$tool->brand}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                 <td style="height:40px; text-align: center;"> <a href="https://mrmonitoring.multi-linegroupofcompanies.com/" target="blank_"><br><button style="margin-top: 20px; border: none;color: white; background-color:#008CBA; padding: 12px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;">View</button></a></td>
                                </tr>
                                <td style="padding: 10px; color: #455056;">Contact Us (Local): <a>2035</a></td>
                                <tr>
                                 <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #455056;">Email: itsupport@multi-linegroup.com</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                                
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>