<?php get_header() ?>
<div class="card">
    <div class="card-header d-flex flex-grow-1 align-items-center">
        <p class="h4 m-0"><?= __('crud.label.edit') ?> <?php get_title() ?></p>
        <div class="right-button ms-auto">
            <a href="<?= crudRoute('crud/index', $tableName) ?>" class="btn btn-warning btn-sm">
                <?= __('crud.label.back') ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if($error_msg): ?>
        <div class="alert alert-danger"><?=$error_msg?></div>
        <?php endif ?>
        <form action="" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row">
            <?php 
            foreach($fields as $key => $field): 
                if($field == '_action_button') continue;
                $label = $field;
                $type  = "text";
                $attr = [
                    'class'=>"form-control"
                ];
                if(is_array($field))
                {
                    $field_data = $field;
                    $field = $key;
                    $label = $field_data['label'];
                    if(isset($field_data['type']))
                    {
                        $type  = $field_data['type'];
                    }

                    if(isset($field_data['attr']))
                    {
                        $attr = array_merge($attr, $field_data['attr']);
                    }
                }
                $label = _ucwords($label);
                $fieldname = $type == 'file' ? $field : $tableName."[".$field."]";
                $attr = array_merge(["placeholder"=>$label,"value"=>$old[$field]??($data->{$field} ?? '')], $attr);
                if(isset($attr['multiple']))
                {
                    $fieldname .= "[]";
                }
                $col = isset($attr['col']) ? $attr['col'] : 'col-12';
            ?>
            <div class="form-group mb-3 <?= $col ?>">
                <label class="mb-2"><?=$label?></label>
                <?= \Core\Form::input($type, $fieldname, $attr) ?>
            </div>
            <?php endforeach ?>
            </div>
            <div class="form-group">
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
<?php get_footer() ?>
