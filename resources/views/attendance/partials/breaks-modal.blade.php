<div class="modal fade" id="breakDetailsModal" tabindex="-1" role="dialog" aria-labelledby="breakDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content break-modal-content">
            <div class="modal-header break-modal-header">
                <div>
                    <h5 class="modal-title" id="breakDetailsModalLabel"><i class="mdi mdi-coffee-outline"></i>Break Details</h5>
                    <div class="break-modal-subtitle" id="breakModalDate"></div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="break-summary-strip d-flex align-items-center justify-content-around">
                <div class="break-summary-item">
                    <div class="break-summary-value" id="breakSummaryCount">0</div>
                    <div class="break-summary-label">Breaks Taken</div>
                </div>
                <div class="break-summary-divider"></div>
                <div class="break-summary-item">
                    <div class="break-summary-value" id="breakSummaryTotal">0h 00m</div>
                    <div class="break-summary-label">Total Break Time</div>
                </div>
            </div>

            <div class="modal-body p-0">
                <div class="break-list-wrap" id="breakDetailsBody"></div>
            </div>
        </div>
    </div>
</div>
