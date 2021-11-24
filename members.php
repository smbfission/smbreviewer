<?php
ob_start();
include_once("header.php");
include_once("sidebar.php");


if ($_SESSION['type'] != 1) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /campaign/");
    exit();
}
?>

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Members</h2>

        <div class="right-wrapper pull-right" style="display:none;">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.html">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><span>Tables</span></li>
                <li><span>Advanced</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <!-- start: page -->
    <section class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="h2 mt-none mb-sm text-dark text-bold">
                        Members
                        <a style="margin-bottom:5px;" class="btn btn-fill btn-primary pull-right" type="submit" href="/members_add/">Add New Members</a>
                    </h2>
                    <p class="text-muted font-13 m-b-15">
                        <!-- Description Goes Here <br /> -->
                        &nbsp;
                    </p>
                </div>


                <?php
                if (isset($_SESSION['msg']) && $_SESSION['msg'] != "") {
                ?>
                    <div class="col-sm-12">
                        <div class="alert alert-info"><?php echo $_SESSION['msg']; ?></div>
                    </div>
                <?php
                    unset($_SESSION['msg']);
                }
                ?>
            </div>


            <table id="datatable-responsive" class="table table-striped  table-colored table-info dt-responsive nowrap" cellspacing="0" width="100%" style="font-size:12px;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th class="text-center">Impressions<br>(30 Days)</th>
                        <th class="text-center">Sign-up date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users = $db->getUsers()) : ?>


                        <?php foreach ($users as $row) : ?>


                            <tr>

                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['email']; ?> </td>

                                <td><small><?= $row['phone']; ?></small> </td>
                                <td>
                                    <?php if ($row['type'] == "1") {
                                        $class = "success";
                                        $label = "Admin";
                                    } else if ($row['type'] == "3") {
                                        $class = "info";
                                        $label = "Editor";
                                    } else {
                                        $class = "info";
                                        $label = "Member";
                                    } ?>
                                    <span class="label label-<?php echo $class; ?>"><?php echo $label; ?></span>
                                </td>
                                <td><small><?= $row['plan_name'] ?><?= ($row['user_plans_name'] == $row['plan_name'] ? "" : "(expired)") ?></small></td>
                                <td>
                                    <?php
                                    switch ($row['status']) {
                                        case '1':
                                            $class = "success";
                                            $label = "Active";
                                            break;
                                        case '2':
                                            $class = "warning";
                                            $label = "Pending";
                                            break;
                                        default:
                                            $class = "danger";
                                            $label = "Blocked";
                                            break;
                                    }

                                    ?>

                                    <span class="label label-<?php echo $class; ?>"><?php echo $label; ?></span>
                                </td>
                                <td class="text-center"><?php echo $row['visit_qty']; ?> </td>

                                <td><small><?= $row['date_add'] ?></small> </td>
                                <td>
                                    <!--- Start of Action Coloum-->
                                    <div class="hidden-sm hidden-xs btn-group">
                                        <a href="/members_add?id=<?php echo $row['id']; ?>" class="btn btn-fill btn-xs btn-info">
                                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                                        </a>

                                        <a onclick="return deleteMe('/action.php?id=<?php echo $row['id']; ?>&action=delete_member')" class="btn btn-fill btn-xs btn-danger">
                                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                        </a>
                                    </div>
                                </td>
                                <!--- End of Action Colum-->
                            </tr>

                        <?php endforeach; ?>



                    <?php endif; ?>
                </tbody>
            </table>



        </div> <!-- container -->


    </section>
    <!-- end: page -->
</section>
</div>

</section>

<?php include_once('footer_default.php'); ?>

<script>
    function deleteMe(url) {
        if (confirm("Are you sure?")) {
            window.location = url;
        }
        return false;
    }

    $('#datatable-responsive').dataTable({
        "language": {
            "emptyTable": "No users found"
        }
    });
</script>


</body>

</html>