<?php require "includes/header.php"; ?>
<?php require "config/config.php";?>
<?php
    if(isset($_GET['id'])){
        $id=$_GET['id'];

        $onePost=$conn->query("SELECT * FROM posts WHERE id='$id'");
        $onePost->execute();

        $posts=$onePost->fetch(PDO::FETCH_OBJ); 
    }
    $comments=$conn->query("SELECT * FROM comments WHERE post_id='$id'");
    $comments->execute();

    $comment=$comments->fetchAll(PDO::FETCH_OBJ);


    
    $ratings = $conn->query("SELECT * FROM rates WHERE post_id='$id' AND user_id ='$_SESSION[user_id]'");
    $ratings->execute();

    $rating = $ratings->fetch(PDO::FETCH_OBJ);


?>
        <div class="row">
            <div class="card mt-5">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $posts->title ;?></h5>
                    <p class="card-text"><?php echo $posts->body;?></p>
                    <form method="POST" id="form-data">
                        <div class="my-rating"></div>
                        <input id="rating" type="hidden" name="rating"/>
                        <input id="post_id" name="post_id" type="hidden" value="<?php echo $posts->id;?>"/>
                        <input id="user_id" name="user_id" type="hidden" value="<?php echo $_SESSION['user_id'];?>"/>
                    </form>
                </div>
            </div>
        </div>

<div class="row">
    <form method="POST" id="comment_data">
    

        <div class="form-floating">
            <input name="username" type="hidden" value="<?php echo $_SESSION['username'];?>" class="form-control" id="username">
        </div>

        <div class="form-floating">
            <input name="post_id" type="hidden" value="<?php echo $posts->id;?>" class="form-control" id="post_id" >
        </div>

        <div class="form-floating mt-4">
            <textarea rows="9" name="comment" placeholder="comment" class="form-control" id="comment" ></textarea>
            <label for="floatingPassword">comment</label>
        </div>

        <button name="submit" id="submit" class="w-100 btn btn-lg btn-primary mt-4" type="submit">Create Comment</button>

        <div id ="msg" class="nothing"></div>
        <div id ="delete-msg" class="nothing"></div>

    </form>
</div>

<div class="row">
    <?php foreach($comment as $singleComment) :?>
        <div class="card mt-5">
            <div class="card-body">
                <h5 class="card-title"><?php echo $singleComment->username ;?></h5>
                <p class="card-text"><?php echo $singleComment->comment;?></p>
                <?php if(isset($_SESSION['username']) AND $_SESSION['username']==$singleComment->username): ?>
                    <button id="delete-btn" value="<?php echo $singleComment->id;?>" class="btn btn-danger mt-3">Delete</button>
                <?php endif;?>
            </div>
        </div>
    <?php endforeach;?>
</div>


<?php require "includes/footer.php"; ?>
<script>
    // A $( document ).ready() block.
    $(document).ready(function() {

        $(document).on('submit',function(e){

            e.preventDefault();

            // alert("")
            var FormData=$("#comment_data").serialize()+'&submit=submit';

            $.ajax({
                type : 'post',
                url : 'insert-comments.php',
                data : FormData,

                success : function(){
                    //alert('sucess');
                    $("#Comment").val(null);
                    $("#username").val(null);
                    $("#post_id").val(null);

                    $("#msg").html("Added Successfully").toggleClass("alert alert-success bg-success text-white mt-3");
                    fetch();
                }
            });
        });

        $("#delete-btn").on('click',function(e){

            e.preventDefault();

            // alert("")
            var id =$(this).val();

            $.ajax({
                type : 'post',
                url : 'delete-comment.php',
                data : {
                    delete : 'delete',
                    id : id
                },

                success : function(){
                    //alert(id);
                    $("#delete-msg").html("Deleted Successfully").toggleClass("alert alert-success bg-success text-white mt-3");
                    fetch();
                }
        });
    });


    function fetch(){
        setInterval(function (){
            $("body").load("show.php?id=<?php echo $_GET['id'];?>");

        },3000);
    }
    // rating system
    $(".my-rating").starRating({
        starSize: 25,
        initialRating: "<?php
            if(isset($rating->rating) AND isset($rating->user_id) AND $rating->user_id == $_SESSION['user_id']){
                echo $rating->rating;
            }else{
                echo '0';
            }       
        ?>" ,

        callback: function(currentRating, $el){
                    // make a server call here
                    $("#rating").val(currentRating);

                    $(".my-rating").click(function(e){
                        e.preventDefault();

                        var formdata =$("#form-data").serialize()+'&insert=insert';

                        $.ajax({
                            type : "POST",
                            url: 'insert-ratings.php',
                            data: formdata,

                            success:function(){
                               // alert(formdata);
                            }
                        })
                    })
    }
});
    

});

</script>
