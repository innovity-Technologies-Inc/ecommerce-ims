<!-- ========== Footer Start ========== -->
@php($gs = \App\HelperClass::generalSettings())
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <script>document.write(new Date().getFullYear())</script> &copy; {{ $gs->business_name ?? 'Smart Ecom' }}. Developed by <a
                    href="https://gen-itech.com" class="fw-bold footer-text" target="_blank">Gen-Itech</a>
            </div>
        </div>
    </div>
</footer>
<!-- ========== Footer End ========== -->
