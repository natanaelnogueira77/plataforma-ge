<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="export-reformations-modal" 
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= _('Exportar Excel') ?></h5>
            </div>
            
            <div class="modal-body">
                <form id="export-reformations">
                    <div class="form-row"> 
                        <div class="form-group col-md-6">
                            <label><?= _('Produto') ?></label>
                            <select name="product_id" class="form-control">
                                <option value=""><?= _('Todos os Produtos') ?></option>
                                <?php 
                                    if($dbProducts) {
                                        foreach($dbProducts as $dbProduct) {
                                            echo "<option value=\"{$dbProduct->id}\">{$dbProduct->desc_short}</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="r_date"><?= _('Data') ?></label>
                            <input type="date" name="r_date" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer d-block text-center">
                <input form="export-reformations" type="submit" class="btn btn-success btn-lg" value="<?= _('Exportar Excel') ?>">
                <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal"><?= _('Voltar') ?></button>
            </div>
        </div>
    </div>
</div>