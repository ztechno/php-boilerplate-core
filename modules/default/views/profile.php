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
            <a href="javascript:void(0)" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#editPasswordModal">Edit Password</a>
            <a href="<?=routeTo('default/edit-profile')?>" class="btn btn-info">Edit Profile</a>
        </div>
    </div>
    <div class="card-body">
        <?php if ($success_msg) : ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
        <?php endif ?>
        <?php if ($error_msg) : ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php endif ?>
        <table class="table">
            <tr>
                <td rowspan="2" width="250px">
                    <img src="<?=asset('assets/default/img/user-default.png')?>" alt="" width="250px">
                </td>
                <td>
                    <table class="table table-bordered">
                        <tr>
                            <td width="250px">Kode</td>
                            <td width="20px">:</td>
                            <td><?=$user->profile?->code?></td>
                        </tr>
                        <tr>
                            <td width="250px">Nama</td>
                            <td width="20px">:</td>
                            <td><?=$user->name?></td>
                        </tr>
                        <tr>
                            <td width="250px">Alamat</td>
                            <td width="20px">:</td>
                            <td><?=$user->profile?->address?></td>
                        </tr>
                        <tr>
                            <td width="250px">Jenis Kelamin</td>
                            <td width="20px">:</td>
                            <td><?=$user->profile?->gender?></td>
                        </tr>
                        <tr>
                            <td width="250px">No. HP</td>
                            <td width="20px">:</td>
                            <td><?=$user->profile?->phone?></td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td>:</td>
                            <td><?=$user->username?></td>
                        </tr>
                        <?php if(strpos(env('APP_MODULES'), 'assessment') > -1): ?>
                        <tr>
                            <td>NIDN</td>
                            <td>:</td>
                            <td><?=$user->assessment_profile?->NIDN?></td>
                        </tr>
                        <tr>
                            <td>Pangkat, Golongan ruang</td>
                            <td>:</td>
                            <td><?=$user->assessment_profile?->pangkat?>, <?=$user->assessment_profile?->golongan?></td>
                        </tr>
                        <tr>
                            <td colspan="3"><b>Jabatan</b></td>
                        </tr>
                        <tr>
                            <td>- Struktural</td>
                            <td>:</td>
                            <td><?=$user->assessment_profile?->jab_struktural?></td>
                        </tr>
                        <tr>
                            <td>- Akademik</td>
                            <td>:</td>
                            <td><?=$user->assessment_profile?->jab_akademik?></td>
                        </tr>
                        <tr>
                            <td colspan="3"><b>Unit Kerja</b></td>
                        </tr>
                        <tr>
                            <td>- Perguruan Tinggi</td>
                            <td>:</td>
                            <td><?=$user->assessment_profile?->perguruan_tinggi?></td>
                        </tr>
                        <tr>
                            <td>- Fakultas</td>
                            <td>:</td>
                            <td><?=$user->assessment_profile?->fakultas?></td>
                        </tr>
                        <tr>
                            <td>- Program Studi</td>
                            <td>:</td>
                            <td><?=$user->assessment_profile?->program_studi?></td>
                        </tr>
                        <?php endif ?>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="modal fade" id="editPasswordModal" tabindex="-1" role="dialog" aria-labelledby="editPasswordForm" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPasswordForm">Edit Password</h5>
      </div>
      <div class="modal-body">
        <form action="<?=routeTo('default/update-password')?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-group mb-3">
                <label for="" class="mb-2">Password Baru</label>
                <input type="password" name="password" id="" class="form-control" required>
            </div>
            <div class="form-group">
                <button class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php if(is_array($files)): ?>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Form Uplaod</h5>
      </div>
      <div class="modal-body">
        <form action="" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-group mb-3">
                <label for="" class="mb-2">File</label>
                <input type="file" class="form-control" name="file">
            </div>
            <div class="form-group mb-3">
                <label for="" class="mb-2">Tipe</label>
                <select name="record_type" id="" class="form-control">
                    <?php foreach($mediaTypes as $type): ?>
                    <option value="<?=$type?>"><?=$type?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="card">
    <div class="card-header d-flex flex-grow-1 align-items-center">
        <p class="h4 m-0">Berkas</p>

        <?php if(auth()->id == $user->id): ?>
        <div class="right-button ms-auto">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Upload Berkas
            </button>
        </div>
        <?php endif ?>
    </div>
    <div class="card-body">
        <div class="table-responsive table-hover">
            <table class="table table-bordered datatable" style="width:100%">
                <thead>
                    <tr>
                        <th width="20px">#</th>
                        <th>File</th>
                        <th>Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($files as $index => $file): ?>
                    <tr>
                        <td><?=$index+1?></td>
                        <td><a href="<?=asset($file->name)?>">Lihat File</a></td>
                        <td><?=$file->record_type?></td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif ?>
<?php get_footer() ?>
