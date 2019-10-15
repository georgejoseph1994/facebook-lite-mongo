<?php
session_start();
if (!isset($_SESSION['user'])) {
    session_destroy();
    header('Location: login.php');
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

    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <link href="https://unpkg.com/vue-bootstrap-typeahead/dist/VueBootstrapTypeahead.css" rel="stylesheet">
    <script src="https://unpkg.com/vue-bootstrap-typeahead"></script>
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

    .frndsButton {
        font-size: 14px;
        background-color: #4267B2;
        cursor: pointer;
    }

    .friends-list {
        height: 70px;
        list-style-type: none;
        margin-bottom: 10px;
    }

    body {
        background-color: #E9EBEE;
    }

    .postHeader {
        background-color: #F5F6F7;
        font-size: .9rem !important;
    }

    .blueText {
        color: #4267B2 !important;
        font-size: .9rem !important;
    }

    .greyText {
        color: #636972 !important;
        font-size: .6rem !important;
    }

    .whitebtn {
        cursor: pointer;
        font-size: .9rem;
        color: #636972;
    }

    .likeBtn {
        float: right;

    }

    .whitebtn:hover {
        color: #4267B2 !important;
    }

    [v-cloak] {
        display: none;
    }
</style>

<body>
    <div id="app" v-cloak>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light fbl-nav">
            <a href="./index.php"><img src="./assets/facebook-1.svg" style="max-height:45px;padding-left:100px"></a>
            <a href="./logout.php"> <input type="button" class="logout-btn  btn" onClick="logout()" value="Logout" /></a>
        </nav>

        <div>
            <div class="row">
                <!-- Left Pannel -->
                <div class="col-md-3">
                    <div class=" card mt-2 ml-2 postHeader text-center py-2 blueText" style="min-height:100px;">
                        <h6>Welcome <?php echo ($_SESSION['user']->screen_name) ?></h6>
                        <a class="blueText" href="./account.php">Manage Account</a>
                        <a class="blueText" href="./deleteaccount.php">Delete Account</a>
                    </div>

                </div>
                <!-- Middle pannel -->
                <div class="col-md-6" style="overflow-y: auto; height: calc( 100vh - 62px );">
                    <!-- Add post -->
                    <div class="card mt-2 mb-2 pb-2">
                        <div class="postHeader pt-2 pl-2">
                            <p>Create Post</p>
                        </div>
                        <div class="input-group">
                            <textarea v-model="newPostBody" class="form-control mx-2" placeholder="Whats on your mind" aria-label="With textarea" maxlength="200"></textarea>
                        </div>
                        <div class="mt-2 mr-2">
                            <input type="button" class="frndsButton btn btn-primary col-2 " value="Post" v-on:click="createPost(newPostBody,null,null)" :disabled=" newPostBody ==''" style="float:right" />
                        </div>
                    </div>

                    <!-- All posts -->
                    <div>
                        <div v-for="(result, index) in allPosts" class="card mb-2 py-2">
                            <div class="pl-4">
                                <p class="blueText mb-0"> {{ result.screen_name }} </p>
                                <p class="greyText"> {{ result.timestamp.split(" ")[0] }} </p>
                            </div>
                            <div class="px-4 ">
                                <p> {{ result.post_body }} </p>
                            </div>
                            <div class="px-4 ">
                                <p class="greyText"> {{ result.likes.length }} Likes </p>
                            </div>
                            <!-- like and comment button -->
                            <hr class="mx-4 mt-0 mb-1">
                            <div class="row">
                                <div class="col-5  whitebtn likeBtn text-right" v-on:click="firstLevelUpdate(index, 'like')" v-if="result.likes.indexOf(userEmail) < 0">Like</div>
                                <div class="col-5  whitebtn likeBtn text-right" v-on:click="firstLevelUpdate(index, 'unLike')" v-if="result.likes.indexOf(userEmail) > -1">Unlike</div>
                                <div class="col-2"></div>
                                <div class="col-5  whitebtn commentBtn text-left" v-on:click="flipComments(index,null)">Comment</div>
                            </div>
                            <hr class="mx-4 mb-4 mt-1">
                            <!-- comment box -->
                            <div v-if="comments[index] == true">
                                <div class="input-group pl-3 pr-3">
                                    <textarea v-model=commentBody[index] class="form-control mx-2" placeholder="Comment" aria-label="With textarea" maxlength="200"></textarea>
                                </div>
                                <div class="mt-2 mr-2 pr-3">
                                    <input type="button" class="frndsButton btn btn-primary col-2 " value="Comment" v-on:click="firstLevelUpdate(index,'comment')" :disabled=" typeof commentBody[index]=== 'undefined' || commentBody[index] ==''" style="float:right" />
                                </div>
                            </div>
                            <!-- Comments of a post -->
                            <div v-for="(comment, index1) in result.comment" class="mb-2 px-5 py-1">
                                <div class="pl-4">
                                    <p class="blueText mb-0"> {{ comment.screen_name }} </p>
                                    <p class="greyText mb-0"> {{ comment.timestamp.split(" ")[0] }} </p>
                                </div>
                                <div class="px-4 py-0 mb-0">
                                    <p class="mb-0"> {{ comment.post_body }} </p>
                                </div>
                                <div class="px-4 py-0 mb-0">
                                    <p class="greyText py-0 mb-0"> {{ comment.likes?comment.likes.length:0 }} Likes </p>
                                </div>
                                <!-- like and comment button -->
                                <hr class="mx-4 mt-0 mb-1">
                                <div class="row">
                                    <div class="col-5  whitebtn likeBtn text-right" v-on:click="secondLevelUpdate(index,index1,'like')" v-if="comment.likes.indexOf(userEmail) < 0">Like</div>
                                    <div class="col-5  whitebtn likeBtn text-right" v-on:click="secondLevelUpdate(index,index1,'unLike')" v-if="comment.likes.indexOf(userEmail) > -1">Unlike</div>
                                    <div class="col-2"></div>
                                    <div class="col-5  whitebtn commentBtn text-left" v-on:click="flipComments(index,index1)">Comment</div>
                                </div>
                                <hr class="mx-4 mb-4 mt-1">
                                <!-- comment box -->
                                <div v-if="secondLevelComments[index1] == true">
                                    <div class="input-group pl-3 pr-3">
                                        <textarea v-model=secondLevelCommentBody[index1] class="form-control mx-2" placeholder="Comment" aria-label="With textarea" maxlength="200"></textarea>
                                    </div>
                                    <div class="mt-2 mr-2 pr-3">
                                        <input type="button" class="frndsButton btn btn-primary col-2 " value="Comment" v-on:click="secondLevelUpdate(index,index1,'comment')" :disabled=" typeof secondLevelCommentBody[index1]=== 'undefined' || secondLevelCommentBody[index1] ==''" style="float:right" />
                                    </div>
                                </div>
                                <!-- comments of comments -->
                                <div v-for="(commentOfComment, index2) in comment.comment" class="mb-2 px-5 py-1">
                                    <div class="pl-4">
                                        <p class="blueText mb-0"> {{ commentOfComment.screen_name }} </p>
                                        <p class="greyText mb-0"> {{ commentOfComment.timestamp.split(" ")[0] }} </p>
                                    </div>
                                    <div class="px-4 py-0 mb-0">
                                        <p class="mb-0"> {{ commentOfComment.post_body }} </p>
                                    </div>
                                    <div class="px-4 py-0 mb-0">
                                        <p class="greyText py-0 mb-0"> {{ commentOfComment.likes?commentOfComment.likes.length:0 }} Likes </p>
                                    </div>
                                    <!-- like and comment button -->
                                    <hr class="mx-4 mt-0 mb-1">
                                    <div class="row">
                                        <div class="col-5  whitebtn likeBtn text-right" v-on:click="thirdLevelUpdate(index,index1,index2,'like')" v-if="commentOfComment.likes.indexOf(userEmail) < 0">Like</div>
                                        <div class="col-5  whitebtn likeBtn text-right" v-on:click="thirdLevelUpdate(index,index1,index2,'unLike')" v-if="commentOfComment.likes.indexOf(userEmail) > -1">Unlike</div>
                                        <div class="col-2"></div>
                                        <!-- <div class="col-5  whitebtn commentBtn text-left" v-on:click="flipComments(index,index1,index2)">Comment1</div> -->
                                    </div>
                                    <hr class="mx-4 mb-4 mt-1">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Right pannel -->
                <div class="col-md-3" style="max-height: calc( 100vh - 62px ) !important;">
                    <div class="card" style="min-height: calc( 100vh - 62px )">
                        <div class="form-group px-4 row ">
                            <input type="text" v-model="search" class="form-control mt-2 col-8" placeholder="Search users">
                            <div class="col-1"></div>
                            <input class="btnSubmit frndsButton btn btn-primary col-2 mt-2 ml-0 " type="button" value="search" v-on:click="searchUser" style="float:right;min-width:70px" />
                        </div>
                        <!-- {{searchResult}} -->
                        <ul id="incomingReq">
                            <li v-for="(item, index) in incommingFriendsReq" class="card friends-list pl-2  mr-4 row" style="">
                                <div class="col-4 pt-3">
                                    {{ item.screen_name }}
                                </div>
                                <div class="col-8 row pt-1">
                                    <button class="btnSubmit frndsButton btn btn-primary mt-2 ml-0 mr-2" v-on:click="respondToFriendsRequest(item.email,'A')">Accept</button>
                                    <button class="btnSubmit frndsButton btn btn-primary mt-2 ml-0 " v-on:click="respondToFriendsRequest(item.email,'N')">Reject</button>
                                </div>
                            </li>
                        </ul>
                        <!-- {{searchResult}} -->
                        <ul id="searchFriends">
                            <li v-for="(user, index) in searchResult" class="card friends-list pl-2 pt-3 mr-4 row" style="" v-if=" friendStatus(user.friendships,userEmail)!='received'">
                                <div class="col-6">
                                    {{ user.screen_name }}
                                </div>
                                <div class="col-6">
                                    <!-- v-on:click="sendFriendsRequest(result.EMAIL)" -->
                                    <button v-if="friendStatus(user.friendships,userEmail)=='not friend'" v-on:click="sendFriendsRequest(user.email,user.screen_name)" class="btn btn-secondary frndsButton">Add</button>
                                    <button v-if="friendStatus(user.friendships,userEmail)=='received'" class=" btn btn-secondary frndsButton ">Reject</button>
                                    <button v-if="friendStatus(user.friendships,userEmail)=='pending'" class="btn btn-secondary frndsButton ">Pending</button>
                                    <button v-if="friendStatus(user.friendships,userEmail)=='rejected'" class="btn btn-secondary frndsButton ">Rejected</button>
                                    <!-- <button v-if="friendStatus(user.friendships,userEmail)=='friend'" class="btn btn-secondary frndsButton ">Friend</button>  -->
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
        // Initialise a new Vue instance
        new Vue({
            el: "#app",
            components: {},
            data: function() {
                return {
                    search: '',
                    searchResult: [],
                    userEmail: "<?php echo ($_SESSION['user']->email) ?>",
                    userScreenName: "<?php echo ($_SESSION['user']->screen_name) ?>",
                    incommingFriendsReq: [],
                    newPostBody: "",
                    allPosts: [],
                    // to store state of comment box
                    comments: [],
                    secondLevelComments: [],
                    thirdLevelComments: [],
                    // These variables have comment body 
                    commentBody: [],
                    secondLevelCommentBody: [],
                    thirdLevelCommentBody: [],
                }
            },

            methods: {
                /*
                * Method to return the status of friendship
                */
                friendStatus: function(friendShipArr, userEmail) {
                    filteredFriendshipArr = friendShipArr.filter((frnd) => {
                        return (frnd.email == userEmail)
                    });
                    if (filteredFriendshipArr.length == 0) {
                        return "not friend";
                    } else if (filteredFriendshipArr[0].status == "R") {
                        return "pending";
                    } else if (filteredFriendshipArr[0].status == "N") {
                        return "rejected";
                    } else if (filteredFriendshipArr[0].status == "A") {
                        return "friend";
                    } else {
                        return "received";
                    }
                },
                /*
                * Method to search users according to a search string
                */
                searchUser: function() {
                    var self = this;
                    let qbody = {
                        method: 'search',
                        search: this.search,
                    }

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
                                self.searchResult = data.users.filter((users) => {
                                    return (users.email != self.userEmail);
                                });
                            } else {
                                console.log(JSON.stringify(data));
                            }
                        })
                        .catch(function(error) {
                            console.log('Request failed', error);
                        });
                },
                /*
                * Method to send a friends request
                */
                sendFriendsRequest: function(email, screen_name) {
                    var self = this;
                    let qbody = {
                        method: "sendReq",
                        screen_name_a: this.userScreenName,
                        screen_name_b: screen_name,
                        user_email_a: this.userEmail,
                        user_email_b: email,
                        status1: 'S',
                    }

                    url = './api.php';
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
                                self.searchUser()
                            } else {
                                console.log(JSON.stringify(data));
                            }
                        })
                        .catch(function(error) {
                            console.log('Request failed', error);
                        });
                },
                /*
                * Method to fetch all friends request
                */
                fetchFriendsRequest: function() {
                    var self = this;
                    self.incommingFriendsReq = [];
                    let qbody = {
                        method: "fetchFriendships",
                        email: this.userEmail,
                    }
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
                                self.incommingFriendsReq = data.results.filter((frnd) => {
                                    return (frnd.status == "R")
                                });
                            } else {
                                console.log(JSON.stringify(data));
                            }
                        })
                        .catch(function(error) {
                            console.log('Request failed', error);
                        });
                },
                /*
                * Method to respond to a friends req (Accept or Reject)
                */
                respondToFriendsRequest: function(email, status) {
                    var self = this;
                    let qbody = {
                        method: "respondReq",
                        user_email_a: email,
                        user_email_b: this.userEmail,
                        status: status,
                    }

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
                                self.fetchFriendsRequest();
                            } else {
                                console.log(JSON.stringify(data));
                            }
                        })
                        .catch(function(error) {
                            console.log('Request failed', error);
                        });
                },
                /*
                *  Method to create a post
                */
                createPost: function(body, parentId, rootParentId) {
                    if (body != "") {
                        var self = this;

                        let qbody = {
                            method: "createPost",
                            post_body: body,
                            email: this.userEmail,
                        }

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
                                    self.fetchAllPosts();
                                    self.newPostBody = "";
                                } else {
                                    console.log(JSON.stringify(data));
                                }
                            })
                            .catch(function(error) {
                                console.log('Post creation failed', error);
                            });
                    }
                },
                /*
                *  Method to fetch all friends request
                */
                fetchAllPosts: function() {
                    var self = this;
                    let qbody = {
                        method: "fetchPost",
                        email: this.userEmail,
                    }
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
                                /*
                                 * Sorting the posts - newer post first
                                 */
                                self.allPosts = (data.postlist).sort((a, b) => {
                                    if (b.timestamp < a.timestamp) {
                                        return -1;
                                    } else if (b.timestamp < a.timestamp) {
                                        return 1;
                                    } else {
                                        return 0;
                                    }
                                });
                                // self.createComment(self.allPosts);


                                // for (i = 0; i < self.allPosts.length; i++) {
                                //     self.comments[i] = false;
                                //     self.commentBody[i] = "";
                                // }
                                console.log(data)
                            } else {
                                console.log(JSON.stringify(data));
                            }
                        })
                        .catch(function(error) {
                            console.log('Request failed', error);
                        });
                },
                /*
                *  Method to update comment of comments of comments
                */
                thirdLevelUpdate: function(index, index1, index2, action) {
                    let post = this.allPosts[index];
                    let SelectedComment = post.comment[index1];
                    let thirdLevelComment = SelectedComment.comment[index2];

                    if (action == 'like') {
                        thirdLevelComment.likes.push(this.userEmail);
                    } else if (action == 'unLike') {
                        thirdLevelComment.likes = thirdLevelComment.likes.filter(e => e !== this.userEmail);
                    } else if (action == 'comment') {
                        let commentObj = {
                            post_body: this.thirdLevelCommentBody[index2],
                            email: this.userEmail,
                            timestamp: new Date().toJSON().replace("T", " "),
                            likes: [],
                            comment: []
                        }
                        thirdLevelComment.comment.push(commentObj);
                        this.flipComments(index, index1, index2);
                        this.thirdLevelCommentBody[index2] = '';
                    }
                    SelectedComment.comment[index2] = thirdLevelComment;
                    post.comment[index1] = SelectedComment;
                    this.allPosts[index] = post;
                    this.firstLevelUpdate(index, "");
                },
                /*
                *  Method to update comment of comments 
                */
                secondLevelUpdate: function(index, index1, action) {
                    let post = this.allPosts[index];
                    let SelectedComment = post.comment[index1];

                    if (action == 'like') {
                        SelectedComment.likes.push(this.userEmail);
                    } else if (action == 'unLike') {
                        SelectedComment.likes = SelectedComment.likes.filter(e => e !== this.userEmail);
                    } else if (action == 'comment') {
                        let commentObj = {
                            post_body: this.secondLevelCommentBody[index1],
                            email: this.userEmail,
                            timestamp: new Date().toJSON().replace("T", " "),
                            likes: [],
                            comment: []
                        }
                        SelectedComment.comment.push(commentObj);
                        this.flipComments(index, index1, null);
                        this.secondLevelCommentBody[index1] = '';
                    }
                    post.comment[index1] = SelectedComment;
                    this.allPosts[index] = post;
                    this.firstLevelUpdate(index, "");
                },
                /*
                *  Method to update comment of comments of comments
                */ 
                firstLevelUpdate: function(index, action) {
                    let post = this.allPosts[index];

                    if (action == 'like') {
                        post.likes.push(this.userEmail);
                    } else if (action == 'unLike') {
                        post.likes = post.likes.filter(e => e !== this.userEmail);
                    } else if (action == 'comment') {
                        let commentObj = {
                            post_body: this.commentBody[index],
                            email: this.userEmail,
                            timestamp: new Date().toJSON().replace("T", " "),
                            likes: [],
                            comment: []
                        }
                        post.comment.push(commentObj);
                        this.flipComments(index, null, null);
                        this.commentBody[index] = '';
                    }

                    var self = this;
                    let qbody = {};
                    url = './api.php';
                    qbody = {
                        method: "updatePost",
                        _id: post._id.$oid,
                        likes: post.likes,
                        comment: post.comment
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
                                self.fetchAllPosts();
                                // console.log(data)
                            } else {
                                console.log(JSON.stringify(data));
                            }
                        })
                        .catch(function(error) {
                            console.log('Request failed', error);
                        });
                },
                /*
                *  Method to hide and show all levels of comment boxs
                */ 
                flipComments: function(index, index1, index2) {
                    if (index1 == null && index2 == null) {
                        if (this.comments[index] == true) {
                            Vue.set(this.comments, index, false);
                        } else {
                            Vue.set(this.comments, index, true);
                        }
                    } else if (index2 == null) {

                        self = this;
                        if (this.secondLevelComments[index1] == true) {
                            Vue.set(self.secondLevelComments, index1, false);
                        } else {
                            Vue.set(self.secondLevelComments, index1, true);
                        }
                       
                    } else {
                        self = this;
                        if (this.thirdLevelComments[index2] == true) {
                            Vue.set(self.thirdLevelComments, index2, false);
                        } else {
                            Vue.set(self.thirdLevelComments, index2, true);
                        }
                       
                    }

                }

            },
            /*
            *  Method to fetch freinds request and post on load
            */ 
            mounted() {
                this.fetchFriendsRequest(),
                this.fetchAllPosts()
            }
        });
    </script>
</body>

</html>