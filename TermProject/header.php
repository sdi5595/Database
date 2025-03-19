<!DOCTYPE html>
<html lang='ko'>
<head>
    <title>BabyCook</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
        .navbar {
            background-color: #87CEEB; /* 하늘색 배경 */
            padding: 10px;
        }
        .navbar .title img {
            vertical-align: middle;
            margin-right: 10px;
        }
        .navbar .title {
            display: flex;
            align-items: center;
        }
        .navbar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .navbar li {
            float: left;
            display: inline;
        }
        .navbar li a {
            display: inline-block;
            color: #ffffff;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        .navbar li:hover {
            background-color: #555;
        }
        .navbar .dropdown {
            display: inline-block;
        }
        .navbar .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .navbar .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }
        .navbar .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .navbar .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>

<div class='navbar fixed'>
    <div class='container'>
        <a class='pull-left title' href="index.php">
            <img src="images/babycook_banner.png" alt="BabyCook" width="40">
            BabyCook
        </a>
        <ul class='pull-right'>
            <li class='dropdown'>
                <a href="javascript:void(0)" class="dropbtn">배송기사관리</a>
                <div class="dropdown-content">
                    <a href='Driver_list.php'>기사 목록</a>
                    <a href='DriverPhone_form.php'>기사 전화 추가/삭제</a>
                    <a href='Car_list.php'>차량 목록</a>
                </div>
            </li>
            <li class='dropdown'>
                <a href="javascript:void(0)" class="dropbtn">배송상태관리</a>
                <!-- 상세항목은 나중에 추가 -->
                <div class="dropdown-content">
                    <a href='Delivery_list.php'>배송조회</a>
                </div>
            </li>
        </ul>
    </div>
</div>
