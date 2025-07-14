
<div id="modal_show_property" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ព័ត៌មានលម្អិត</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_salary" action="" method="POST" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <h5 class="text-danger">Action Log:  <span class="activity_log"></span></h5>

                    <table class="table">
                        <tbody class="log_lists">

                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
