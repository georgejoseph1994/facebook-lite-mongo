<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
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

    body {
        background-color: #E9EBEE;
    }

    .btnSubmit {
        display: block;
        margin: 0 auto;
        width: 100px;
    }
    .green{
        background-color: green;;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fbl-nav">
        <a href="./index.php"><img src="./assets/facebook-1.svg" style="max-height:45px;padding-left:100px"></a>
    </nav>

    <div class="container ">
        <div class="mx-auto">
            <div class="row">
                <div class="col-md-3">

                </div>
                <div class="card col-md-6 py-5 mt-5">
                    <h3 class="text-center py-2">Login</h3>
                    <form>
                        <div class="form-group">
                            <input type="text" id="sign_up_email" class="form-control" placeholder="Email *" value="" />
                        </div>
                        <div class="form-group">
                            <input type="password" id="sign_up_password" class="form-control" placeholder="Password *" value="" />
                        </div>
                        <div class="form-group">
                            <input type="button" class="btnSubmit btn btn-primary" value="Login" onclick="login()" />
                        </div>
                        <div class="form-group">
                            <a href="./signup.php"><input type="button" href="./signup.php" class="btnSubmit btn btn-primary green" value="signup"  /></a>
                        </div>
                        <div class="alert alert-danger mt-5" id="login_err" style="display:none" role="alert">
                            Error. The credentials you have entered is invalid.
                        </div>
                        <div class="alert alert-primary mt-5" id="login_succ" style="display:none" role="alert">
                            User Login Succesfully.
                        </div>
                    </form>
                </div>
                <div class="col=md-3">
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    /*
    * Method to log user into facebook lite
    */
    function login() {

        document.getElementById('login_err').style.display = 'none';
        document.getElementById('login_succ').style.display = 'none';
        
        //body of the request
        let qbody = {
            method:'login',
            email: document.getElementById('sign_up_email').value,
            password: document.getElementById('sign_up_password').value,
        }
        
        /*
        * Sending request to the server to login.
        */
        url = './api.php'
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
                    document.getElementById('login_succ').style.display = 'block';
                    setTimeout(() => {
                        window.location.href = '/index.php';
                    }, 1000);
                } else {
                    // console.log(JSON.stringify(data));
                    document.getElementById('login_err').style.display = 'block';
                }
            })
            .catch(function(error) {
                document.getElementById('login_err').style.display = 'block';
                console.log('Request failed', error);
            });
    }
</script>

</html>