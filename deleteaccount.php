<?php
session_start();
//  session_destroy();
// echo json_encode($_SESSION['user']);
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <title>Facebook Lite</title>
</head>
<style>
    .fbl-nav {
        background-color: #3b5998;
        background-image: linear-gradient(#4e69a2, #3b5998 50%);
        border-bottom: 1px solid #133783;
        min-height: 42px;
        position: relative;
        z-index: 1;
    }

    .logout-btn:hover {
        color: #3b6000;
        background-color: white;
    }

    .logout-btn {
        background-color: #3b5998;
        color: white;
        border: solid;
        border-color: white;
        border-width: 1px;
        width: 100px;
        cursor: pointer;
        position: absolute;
        right: 20px;
        margin-top: -18px;
    }

    body {
        background-color: #E9EBEE;
    }

    .deletebtn {
        background-color: #FA3E3E;
    }

    .deletebtn:hover {
        background-color: red;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fbl-nav">
        <a href="./index.php"><img src="./assets/facebook-1.svg" style="max-height:45px;padding-left:100px"></a>
        <a href="./logout.php"> <input class="logout-btn  btn" onClick="logout()" value="Logout" /></a>
    </nav>

    <div>
        <div class="row pt-5">
            <div class="col-4">
            </div>
            <div class="col-4 card ">
                <div class="py-4 text-center">
                    <h2 class="text-center" style=" color: #636972 ">Delete Account</h2>
                    <h5 style=" color: #636972">Are you sure you want to delete the account?</h4>
                        <button class="btn deletebtn" onclick="deleteUser()"> Delete My Account</button>
                </div>
                <div class="alert alert-danger mt-5" id="del_err" style="display:none" role="alert">
                    Error. Deletion failed
                </div>
                <div class="alert alert-primary mt-5" id="del_succ" style="display:none" role="alert">
                    User Deleted Succesfully
                </div>
            </div>
            <div class="col-4">
            </div>
        </div>


</body>
<script type="text/javascript">
    document.getElementById('del_err').style.display = 'none';
    document.getElementById('del_succ').style.display = 'none';

    function deleteUser() {
        url = './api.php'
        let qbody = {
            method:"deleteUser",
            email: "<?php echo ($_SESSION['user']->email) ?>"
        }
        fetch(url, {
                method: 'post',
                headers: {
                    "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
                },
                body: JSON.stringify(qbody)
            })
            .then((response) => response.json())
            .then(function(data) {
                if (data.status == "Success") {
                    document.getElementById('del_succ').style.display = 'block';
                    setTimeout(() => {
                        window.location.href = '/login.php';
                    }, 1000);

                } else {
                    console.log(JSON.stringify(data));
                    document.getElementById('del_err').style.display = 'block';
                }
            })
            .catch(function(error) {
                console.log('Request failed', error);
            });

        // 
    }
</script>

</html>