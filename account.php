<?php
session_start();
//  session_destroy();
echo json_encode($_SESSION['user']);
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
    .logout-btn:hover{
        color: #3b6000;
        background-color: white;
    }
    .logout-btn{
        background-color: #3b5998;
        color:white;
        border: solid;
        border-color: white;
        border-width: 1px; 
        width: 100px; 
        cursor: pointer;
        position:absolute;
        right:20px;
        margin-top: -18px;
    }
    body {
        background-color: #E9EBEE;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fbl-nav">
        <a href="./index.php"><img  src="./assets/facebook-1.svg" style="max-height:45px;padding-left:100px"></a>
        <a href="./logout.php"> <input class="logout-btn  btn" onClick="logout()" value="Logout" /></a>
    </nav>

    <div>
        <div class="row pt-5">
            <div class="col-4">
            </div>
            <div class="col-4 card ">
                <div class="py-4 ">
                    <h2 class="text-center">My Account</h2>
                    <form >
                        <div class="form-group ">
                            <label for="email">Email</label>
                            <input type="email" id="reg-email" class="form-control" name="email" placeholder="Email *" value="<?php echo $_SESSION['user']->email ?>" required readonly />
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="reg-password" class="form-control" placeholder="Password *" required minlength="8" value="<?php echo $_SESSION['user']->password ?>" />
                        </div>

                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" name="full_name" id="fullName" class="form-control" placeholder="Full Name *" value="<?php echo $_SESSION['user']->full_name ?>" required />
                        </div>

                        <div class="form-group">
                            <label for="screen_name">Screen Name</label>
                            <input type="text" name="screen_name" id="screen-name" class="form-control" placeholder="Screen Name *" value="<?php echo $_SESSION['user']->screen_name ?>" required minlength="8" />
                        </div>

                        <div class="form-group">
                            <label for="dob">Date Of Birth</label>
                            <input type="date" name="dob" id="dob" class="form-control" placeholder="Date of Birth *" value="<?php echo $_SESSION['user']->dob ?>" required />
                        </div>

                        <label for="gender">Gender</label>
                        <div class="input-group mb-3">
                            <select class="custom-select" name="gender" id="gender" required>
                                <option value="">Gender *</option>
                                <option value="M" <?php if ($_SESSION['user']->gender == "M")  echo ' selected="selected"'; ?>>Male</option>
                                <option value="F" <?php if ($_SESSION['user']->gender == "F")  echo ' selected="selected"'; ?>>Female</option>
                                <option value="O" <?php if ($_SESSION['user']->gender == "O")  echo ' selected="selected"'; ?>>Other</option>
                            </select>
                        </div>

                        <label for="status">Status</label>
                        <div class="input-group mb-3">
                            <select class="custom-select" id="status" name="status" required>
                                <option value="">Status *</option>
                                <option value="S" <?php if ($_SESSION['user']->status == "S")  echo ' selected="selected"'; ?>>Single</option>
                                <option value="M" <?php if ($_SESSION['user']->status == "M")  echo ' selected="selected"'; ?>>Married</option>
                                <option value="I" <?php if ($_SESSION['user']->status == "I")  echo ' selected="selected"'; ?>>Its Complicated</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" class="form-control" placeholder="Location *" value="<?php echo $_SESSION['user']->location ?>" required />
                        </div>

                        <label for="visibility">visibility</label>
                        <div class="input-group mb-3">
                            <select class="custom-select" id="visibility" name="visibility" required>
                                <option value="">Visibility *</option>
                                <option value="E" <?php if ($_SESSION['user']->visibility == "E")  echo ' selected="selected"'; ?>>Everyone</option>
                                <option value="F" <?php if ($_SESSION['user']->visibility == "F")  echo ' selected="selected"'; ?>>Friends Only</option>
                                <option value="P" <?php if ($_SESSION['user']->visibility == "P")  echo ' selected="selected"'; ?>>Private</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input class="btnSubmit btn btn-primary " onClick="update()" value="Update" />
                        </div>
                        <div class="alert alert-danger" id="reg_err" style="display:none" role="alert">
                            Error. Invalid input data not updated.
                        </div>
                        <div class="alert alert-primary" id="reg_succ" style="display:none" role="alert">
                            Account details updated successfully.
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-4">
            </div>
        </div>


</body>
<script type="text/javascript">
    function update() {
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

        url = './api.php/account/update'

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

            // 
    }
</script>

</html>