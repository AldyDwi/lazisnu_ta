<?php 
echo $this->include('admin/layout/view/header.php');
echo $this->include('admin/layout/view/sidebar.php');
echo $this->renderSection('content');
echo $this->include('admin/layout/view/footer.php');
?>