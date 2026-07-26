<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">

            <!-- Modal Header -->
            <div class="modal-header">
                <div>
                    <h5 class="modal-title font-weight-bold" id="resetPasswordModalLabel">
                        Reset Password
                    </h5>
                    <small class="text-muted">
                        Enter your registered Gmail address
                    </small>
                </div>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">

                <div class="text-center mb-4">
                    <i class="fas fa-lock fa-3x text-primary mb-3"></i>

                    <p class="text-muted mb-0">
                        We'll send a password reset link to your registered Gmail account.
                    </p>
                </div>

                <form action="index.php?url=auth-forgot-password" method="POST">

                    <div class="form-group">
                        <label for="reset_email" class="font-weight-bold">
                            Gmail Address
                        </label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>

                            <input
                                type="email"
                                name="email"
                                id="reset_email"
                                class="form-control"
                                placeholder="example@gmail.com"
                                required
                                autocomplete="email">
                        </div>

                        <small class="form-text text-muted">
                            Use the Gmail address registered with your employee account.
                        </small>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button
                            type="button"
                            class="btn btn-light mr-2"
                            data-dismiss="modal">
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i>
                            Send Reset Link
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>