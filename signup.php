<?php
session_start();
// session_destroy();
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

    .btnSubmit{
        cursor: pointer;
    }

    body {
        background-color: #E9EBEE;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fbl-nav">
        <a href="./index.php"><img  src="./assets/facebook-1.svg" style="max-height:45px;padding-left:100px"></a>
    </nav>

    <div class="container ">
        <div class="mx-auto">
            <div class="row">
                <div class="col-md-3">

                </div>
                <div class=" card col-md-6 py-5 mt-5">
                    <h3 class="text-center py-2">Sign up</h3>
                    <form>
                        <div class="form-group">
                            <input type="email" id="reg-email" class="form-control" placeholder="Email *" value="" required />
                        </div>

                        <div class="form-group">
                            <input type="password" id="reg-password" class="form-control" placeholder="Password *" value="" required minlength="8" />
                        </div>

                        <div class="form-group">
                            <input type="text" id="fullName" class="form-control" placeholder="Full Name *" value="" required />
                        </div>

                        <div class="form-group">
                            <input type="text" id="screen-name" class="form-control" placeholder="Screen Name *" value="" required minlength="8" />
                        </div>

                        <div class="form-group">
                            <input type="date" id="dob" class="form-control" placeholder="Date of Birth *" value="" required />
                        </div>

                        <div class="input-group mb-3">
                            <select class="custom-select" id="gender" required>
                                <option value="">Gender *</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                                <option value="O">Other</option>
                            </select>
                        </div>

                        <div class="input-group mb-3">
                            <select class="custom-select" id="status" required>
                                <option value="">Status *</option>
                                <option value="S">Single</option>
                                <option value="M">Married</option>
                                <option value="I">Its Complicated</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="text" id="location" class="form-control" placeholder="Location *" value="" required />
                        </div>


                        <div class="input-group mb-3">
                            <select class="custom-select" id="visibility" required>
                                <option value="">Visibility *</option>
                                <option value="E">Everyone</option>
                                <option value="F">Friends Only</option>
                                <option value="P">Private</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input class=" btn btn-primary btnSubmit" value="Sign up" onClick="register()" />
                        </div>
                        <div class="alert alert-danger" id="reg_err" style="display:none" role="alert">
                            Error. The credentials you have entered is invalid.
                        </div>
                        <div class="alert alert-primary" id="reg_succ" style="display:none" role="alert">
                            User Succesfully registered.
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
    function register() {
        console.log("hi")
        dateArr = document.getElementById('dob').value.split("-")
        dateval = dateArr[2] + "/" + dateArr[1] + "/" + dateArr[0];

        document.getElementById('reg_err').style.display = 'none';
        document.getElementById('reg_succ').style.display = 'none';

        let qbody = {
            email: document.getElementById('reg-email').value,
            password: document.getElementById('reg-password').value,
            full_name: document.getElementById('fullName').value,
            screen_name: document.getElementById('screen-name').value,
            dob: dateval,
            gender: document.getElementById('gender').value,
            status: document.getElementById('status').value,
            location: document.getElementById('location').value,
            visibility: document.getElementById('visibility').value
        }

        url = './api.php/signup'
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
                    // console.log('', JSON.stringify(data));
                    document.getElementById('reg_succ').style.display = 'block';

                } else {
                    console.log(JSON.stringify(data));

                    document.getElementById('reg_err').style.display = 'block';
                }
            })
            .catch(function(error) {
                console.log('Request failed', error);
            });
    }

    function login() {

        document.getElementById('sign_up_err').style.display = 'none';
        document.getElementById('sign_up_succ').style.display = 'none';

        let qbody = {
            email: document.getElementById('sign_up_email').value,
            password: document.getElementById('sign_up_password').value,
        }

        url = './api.php/login'
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
                    document.getElementById('sign_up_succ').style.display = 'block';
                } else {
                    console.log(JSON.stringify(data));
                    document.getElementById('sign_up_err').style.display = 'block';
                    setTimeout(() => {

                    }, 2000);
                }
            })
            .catch(function(error) {
                console.log('Request failed', error);
            });
    }
</script>

</html>