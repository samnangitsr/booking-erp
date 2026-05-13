{{-- Theme JS shipped with the backend bundle (loaded from public/assets/backend) --}}
<script src="{{ asset('assets/backend') }}/assets/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/backend') }}/assets/js/jquery.min.js"></script>
<script src="{{ asset('assets/backend') }}/assets/plugins/simplebar/js/simplebar.min.js"></script>
<script src="{{ asset('assets/backend') }}/assets/plugins/metismenu/js/metisMenu.min.js"></script>
<script src="{{ asset('assets/backend') }}/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<script src="{{ asset('assets/backend') }}/assets/js/pace.min.js"></script>
<script src="{{ asset('assets/backend') }}/assets/js/app.js"></script>

{{-- PHPFlasher renders SweetAlert success/error notifications fired from the backend --}}
@flasher_render

@stack('scripts')
