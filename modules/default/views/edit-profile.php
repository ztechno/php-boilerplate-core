<?php get_header() ?>
<style>
table td img {
    max-width:150px;
}
</style>
<div class="card">
    <div class="card-header d-flex flex-grow-1 align-items-center">
        <p class="h4 m-0"><?php get_title() ?></p>
        <div class="right-button ms-auto">
            <a href="<?=routeTo('default/profile')?>" class="btn btn-warning">Kembali</a>
        </div>
    </div>
    <div class="card-body">
        <form method="post">
            <?= csrf_field() ?>
            <div class="form-group mb-2">
                <label for="" class="mb-1">Kode</label>
                <input type="text" class="form-control" name="profile[code]" value="<?=$user->profile?->code?>">
            </div>
            <div class="form-group mb-2">
                <label for="" class="mb-1">Nama</label>
                <input type="text" class="form-control" name="users[name]" value="<?=$user->name?>">
            </div>
            <div class="form-group mb-2">
                <label for="" class="mb-1">Alamat</label>
                <textarea class="form-control" name="profile[address]"><?=$user->profile?->address?></textarea>
            </div>
            <div class="form-group mb-2">
                <label for="" class="mb-1">Jenis Kelamin</label>
                <select name="profile[gender]" id="" class="form-control">
                    <option value="">Pilih</option>
                    <option value="MALE" <?=$user->profile?->gender == 'MALE' ? 'selected=""' : ''?>>Laki-laki</option>
                    <option value="FEMALE" <?=$user->profile?->gender == 'FEMALE' ? 'selected=""' : ''?>>Perempuan</option>
                </select>
            </div>
            <div class="form-group mb-2">
                <label for="" class="mb-1">No. HP</label>
                <input type="tel" class="form-control" name="profile[phone]" value="<?=$user->profile?->phone?>">
            </div>
            <?php if(strpos(env('APP_MODULES'), 'assessment') > -1): ?>
            <div class="form-group mb-2">
                <label for="" class="mb-1">NIDN</label>
                <input type="text" class="form-control" name="assessment_profiles[NIDN]" value="<?=$user->assessment_profile?->NIDN?>">
            </div>
            <div class="form-group mb-2">
                <label for="" class="mb-1">Pangkat</label>
                <input type="text" class="form-control" name="assessment_profiles[pangkat]" value="<?=$user->assessment_profile?->pangkat?>">
            </div>
            <div class="form-group mb-2">
                <label for="" class="mb-1">Golongan</label>
                <input type="text" class="form-control" name="assessment_profiles[golongan]" value="<?=$user->assessment_profile?->golongan?>">
            </div>
            <div class="form-group mb-2">
                <label for="" class="mb-1">Jabatan Struktural</label>
                <input type="text" class="form-control" name="assessment_profiles[jab_struktural]" value="<?=$user->assessment_profile?->jab_struktural?>">
            </div>
            <div class="form-group mb-2">
                <label for="" class="mb-1">Jabatan Akademik</label>
                <input type="text" class="form-control" name="assessment_profiles[jab_akademik]" value="<?=$user->assessment_profile?->jab_akademik?>">
            </div>
            <div class="form-group mb-2">
                <label for="" class="mb-1">Perguruan Tinggi</label>
                <input type="text" class="form-control" name="assessment_profiles[perguruan_tinggi]" value="<?=$user->assessment_profile?->perguruan_tinggi?>">
            </div>
            <div class="form-group mb-2">
                <label for="" class="mb-1">Fakultas</label>
                <input type="text" class="form-control" name="assessment_profiles[fakultas]" value="<?=$user->assessment_profile?->fakultas?>">
            </div>
            <div class="form-group mb-2">
                <label for="" class="mb-1">Program Studi</label>
                <input type="text" class="form-control" name="assessment_profiles[program_studi]" value="<?=$user->assessment_profile?->program_studi?>">
            </div>
            <?php endif ?>

            <div class="form-group">
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>
