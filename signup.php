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

    .btnSubmit {
        cursor: pointer;
    }

    body {
        background-color: #E9EBEE;
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
                <div class=" card col-md-6 py-5 mt-5">
                    <h3 class="text-center py-2">Sign up</h3>
                    <div>
                        <div class="form-group">
                            <input type="email" id="reg-email" class="form-control" placeholder="Email *" value="" required />
                        </div>

                        <div class="form-group">
                            <input type="password" id="reg-password" class="form-control" placeholder="Password *" value="" required minlength="8" />
                        </div>

                        <div class="form-group">
                            <input type="text" id="fullName" class="form-control" placeholder="Full Name" value="" />
                        </div>

                        <div class="form-group">
                            <input type="text" id="screen-name" class="form-control" placeholder="Screen Name *" value="" />
                        </div>

                        <div class="form-group">
                            <input type="date" id="dob" class="form-control" placeholder="Date of Birth" value="" />
                        </div>

                        <div class="input-group mb-3">
                            <select class="custom-select" id="gender" required>
                                <option value="">Gender</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                                <option value="O">Other</option>
                            </select>
                        </div>

                        <div class="input-group mb-3">
                            <select class="custom-select" id="status" required>
                                <option value="">Status</option>
                                <option value="S">Single</option>
                                <option value="M">Married</option>
                                <option value="I">Its Complicated</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="text" id="location" class="form-control" placeholder="Location" value="" />
                        </div>


                        <div class="input-group mb-3">
                            <select class="custom-select" id="visibility" required>
                                <option value="">Visibility *</option>
                                <option value="E">Everyone</option>
                                <option value="F">Friends Only</option>
                                <option value="P">Private</option>
                            </select>
                        </div>

                        <div class="form-group text-center">
                            <input type="button" class=" btn btn-primary btnSubmit" value="Sign up" onClick="register()" />
                        </div>
                        <div class="alert alert-danger" id="reg_err" style="display:none" role="alert">
                            Error !!!
                            <ul id="errorNode">
                            </ul>
                        </div>
                        <div class="alert alert-primary" id="reg_succ" style="display:none" role="alert">
                            User Succesfully registered.
                        </div>
                    </div>
                </div>
                <div class="col=md-3">
                </div>
            </div> 
        </div>
    </div>
</body>

</html>
<script>

    // function to register a user
    function register() {
        dateArr = document.getElementById('dob').value.split("-")
        dateval = dateArr[2] + "/" + dateArr[1] + "/" + dateArr[0];

        //hiding the error and sucess 
        document.getElementById('reg_err').style.display = 'none';
        document.getElementById('reg_succ').style.display = 'none';

        // validating user input
        let isValidInputs = validateRegisterUserInput();

        // if all the inputs are valid send a request to the api
        if (isValidInputs) {
            let qbody = {
                method: 'signup',
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

            url = './api.php'

            // sending request to the server
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
                        document.getElementById('reg_succ').style.display = 'block';
                        setTimeout(function(){ location.reload(); }, 3000);
                    } else {
                        // console.log(JSON.stringify(data));
                        document.getElementById('reg_err').style.display = 'block';
                    }
                })
                .catch(function(error) {
                    errorList.push(" This email already exists")

                    for (i = 0; i < errorList.length; i++) {
                        var liElement = document.createElement('li');
                        var txt = document.createTextNode(errorList[i])
                        liElement.appendChild(txt);
                        document.getElementById("errorNode").appendChild(liElement);
                    }

                    document.getElementById('reg_err').style.display = 'block';
                    // console.log('Request failed', error);
                });
        }


    }

    /*
     * method to validate inputs
     * Returns true if all inputs are valid
     * Returns false if any invalid inputs
     */
    function validateRegisterUserInput() {
        // console.log("inside validate")

        // Getting all the values from UI.
        email = document.getElementById('reg-email').value;
        password = document.getElementById('reg-password').value;
        full_name = document.getElementById('fullName').value;
        screen_name = document.getElementById('screen-name').value;
        dob = dateval;
        gender = document.getElementById('gender').value;
        status = document.getElementById('status').value;
        location1 = document.getElementById('location').value;
        visibility = document.getElementById('visibility').value;

        //defining a array for error statements
        errorList = [];

        //validation rules
        // email validation
        if (email == "") {
            errorList.push("Email is required");
        }
        if (!validateEmail(email)) {
            errorList.push("Not a valid email");
        }

        // password validation
        if (password == "") {
            errorList.push("Password is required");
        } else if (password.length < 8) {
            errorList.push("Passwords should be at least 8 charaters");
        } else if (password.length > 15) {
            errorList.push("Passwords should be less than 15 charaters");
        }

        // password validation
        if (screen_name == "") {
            errorList.push("Screen name is required");
        } else if (screen_name.length < 6) {
            errorList.push("Screen Name should be at least 6 charaters");
        } else if (password.length > 15) {
            errorList.push("Screen Name should be less than 15 charaters");
        }

        // password validation
        if (visibility == "") {
            errorList.push("Visibility is required");
        }

        // Deleting all the existing error nodes from dom
        const myNode = document.getElementById("errorNode");
        while (myNode.lastChild) {
            myNode.removeChild(myNode.lastChild);
        }

        // Injecting the errors to the dom
        for (i = 0; i < errorList.length; i++) {
            var liElement = document.createElement('li');
            var txt = document.createTextNode(errorList[i])
            liElement.appendChild(txt);
            document.getElementById("errorNode").appendChild(liElement);
        }
        //returning true or false
        if (errorList.length == 0)
            return true;
        else{
            document.getElementById('reg_err').style.display = 'block';
            return false;
        }
    }

    /*
    *   method to validate email
    */
    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

</script>