<?php
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $DriverID = $_POST['DriverID'];
    $DriverName = $_POST['DriverName'];
    $Area = $_POST['Area'];
    $PhoneNumber = $_POST['PhoneNumber'];
    $CarModels = $_POST['CarModels'];
    $CarMileages = $_POST['CarMileages'];

    $conn = dbconnect($host, $dbid, $dbpass, $dbname);

    // 이미 등록된 DriverID인지 확인
    $check_query = "SELECT * FROM DeliveryDriver WHERE DriverID = '$DriverID'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // 이미 등록된 DriverID인 경우
        msg('이미 등록된 배송기사 ID입니다.');
        echo "<meta http-equiv='refresh' content='0;url=Driver_form.php'>";
    } else {
        // 트랜잭션 시작
        mysqli_query($conn, "set autocommit = 0");
        mysqli_query($conn, "set session transaction isolation level serializable");
        mysqli_query($conn, "begin");

        try {
            // 새로운 DriverID인 경우
            $query = "INSERT INTO DeliveryDriver (DriverID, DriverName, Area) VALUES ('$DriverID', '$DriverName', '$Area')";
            mysqli_query($conn, $query);

            // 휴대폰 번호 등록
            if (!empty($PhoneNumber)) {
                $phone_numbers = explode(',', $PhoneNumber);
                foreach ($phone_numbers as $phone) {
                    $phone = trim($phone);
                    $phone_query = "INSERT INTO DriverPhone (DriverID, PhoneNumber) VALUES ('$DriverID', '$phone')";
                    mysqli_query($conn, $phone_query);
                }
            }

            // 차량 정보 등록
            $car_models = explode(',', $CarModels);
            $car_mileages = explode(',', $CarMileages);
            if (count($car_models) != count($car_mileages)) {
                throw new Exception('차량 모델명과 연비의 개수가 일치하지 않습니다.');
            }
            for ($i = 0; $i < count($car_models); $i++) {
                $model = trim($car_models[$i]);
                $mileage = trim($car_mileages[$i]);
                $car_query = "INSERT INTO Car (DriverID, Model, Mileage) VALUES ('$DriverID', '$model', '$mileage')";
                mysqli_query($conn, $car_query);
            }

            // 트랜잭션 커밋
            mysqli_query($conn, "commit");

            echo '<script>
                    alert("성공적으로 추가되었습니다.");
                    window.location.href = "Driver_list.php";
                  </script>';
        } catch (Exception $e) {
            // 트랜잭션 롤백
            mysqli_query($conn, "rollback");

            msg('추가에 실패했습니다: ' . mysqli_error($conn));
            echo "<meta http-equiv='refresh' content='0;url=Driver_form.php'>";
        }

        mysqli_close($conn);
    }
}
