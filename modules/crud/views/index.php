<?php get_header() ?>
<style>
table td img {
    max-width:150px;
}
table.table td, table.table th {
    white-space:nowrap;
}
</style>
<div class="card">
    <div class="card-header d-flex flex-grow-1 align-items-center">
        <p class="h4 m-0"><?php get_title() ?></p>
        <div class="right-button ms-auto">
            <?= $crudRepository->additionalButtonBeforeCreate() ?>
            <?php if(is_allowed(parsePath(routeTo('crud/create', ['table'=>$tableName])), auth()->id)): ?>
            <a href="<?= crudRoute('crud/create', $tableName) ?>" class="btn btn-success btn-sm">
                <i class="fa-solid fa-plus"></i> <?= __('crud.label.create') ?>
            </a>
            <?php endif ?>
            <?= $crudRepository->additionalButtonAfterCreate() ?>
        </div>
    </div>
    <div class="card-body">
        <?php if ($success_msg) : ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
        <?php endif ?>
        <?php if ($error_msg) : ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php endif ?>
        <div class="table-responsive">
            <table class="table table-striped datatable-crud" style="width:100%">
                <thead>
                    <tr>
                        <th width="20px">#</th>
                        <?php 
                        $isActionButton = false;
                        foreach($fields as $field): 
                            $label = $field;
                            if(is_array($field))
                            {
                                $label = $field['label'];
                            }
                            if($label == '_action_button')
                            {
                                $isActionButton = true;
                            }
                            $label = $label == '_action_button' ? __('crud.label.action_button') : _ucwords($label);
                        ?>
                        <th><?=$label?></th>
                        <?php endforeach ?>
                        <?php if(!$isActionButton): ?>
                        <th class="text-right">
                        </th>
                        <?php endif ?>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php get_footer() ?>
