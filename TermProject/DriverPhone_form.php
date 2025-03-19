<?php
include "header.php";
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수
?>
<div class="container">
    <h2>배송기사 휴대전화 관리</h2>
    <form action="DriverPhone_insert.php" method="post">
        <div class="form-group">
            <label for="DriverID">배송기사 ID:</label>
            <input type="text" class="form-control" id="DriverID" name="DriverID" required>
        </div>
        <div class="form-group">
            <label for="PhoneNumber">휴대폰 번호:</label>
            <input type="text" class="form-control" id="PhoneNumber" name="PhoneNumber" required>
        </div>
        <button type="submit" class="btn btn-primary">등록</button>
    </form>
    <form action="DriverPhone_delete.php" method="post" style="margin-top: 20px;">
        <div class="form-group">
            <label for="DriverID">배송기사 ID:</label>
            <input type="text" class="form-control" id="DriverID" name="DriverID" required>
        </div>
        <div class="form-group">
            <label for="PhoneNumber">휴대폰 번호:</label>
            <input type="text" class="form-control" id="PhoneNumber" name="PhoneNumber" required>
        </div>
        <button type="submit" class="btn btn-danger">삭제</button>
    </form>
</div>
