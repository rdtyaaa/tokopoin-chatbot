<section class="newsletter pb-80 pt-80">
    <div class="news-vector">
        <svg  version="1.1" x="0" y="0" viewBox="0 0 48 48"   xml:space="preserve" ><g><path d="M45 15h-9.18a12 12 0 1 0-23.64 0H3a3 3 0 0 0-3 3v26a3 3 0 0 0 3 3h42a3 3 0 0 0 3-3V18a3 3 0 0 0-3-3ZM2 18.29l13.91 11.38L2 43.59ZM17.47 31c2.19 1.79 3.6 3.29 6.53 3.29s4.33-1.49 6.53-3.29l14.06 14H3.41Zm14.62-1.28L46 18.29v25.3ZM24 3a10 10 0 1 1-10 10A10 10 0 0 1 24 3Zm11.3 14h9.12L27.17 31.12a5 5 0 0 1-6.34 0L3.58 17h9.12a12 12 0 0 0 22.6 0Z"  opacity="1" data-original="#000000" ></path><path d="M21.11 20.46a1 1 0 0 0 .72-1.86 6 6 0 1 1 8-4.32.94.94 0 0 1-1.83-.22V10a1 1 0 0 0-1.92-.4 4 4 0 1 0 .62 6.34 2.94 2.94 0 0 0 5.12-1.23 8 8 0 1 0-10.71 5.75ZM24 15a2 2 0 1 1 2-2 2 2 0 0 1-2 2Z"  opacity="1" data-original="#000000" ></path></g></svg>
    </div>
    <div class="Container">
        <div class="row">
            <div class="col-lg-7 order-2">
                <div class="newsletter-left">
                    <h2>{{@frontend_section_data($news_latter->value,'heading')}} </h2>
                    <span>{{@frontend_section_data($news_latter->value,'sub_heading')}}</span>
                    <form   method="post" action="{{ route('newslatter.subscribe') }}" class="newsletter-form">
                        @csrf
                        <div class="newsletter-form-input">
                            <input type="email" name="email" value="{{old('email')}}" placeholder="Enter your email address" class="newsletter-email">
                            <button type="submit" class="newsletter-submit wave-btn"> <i class="fa-regular fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5 d-flex justify-content-lg-start justify-content-start order-1">
                <div class="need-help">
                    <div class="icon">

                        <svg  version="1.1" x="0" y="0" viewBox="0 0 422.139 422.139"   xml:space="preserve" ><g><path d="M363.631 174.498h-1.045v-25.6C362.586 66.664 295.923 0 213.688 0S64.79 66.664 64.79 148.898v25.6h-6.269c-22.988 0-40.751 20.375-40.751 43.886v65.306c-.579 22.787 17.425 41.729 40.212 42.308.18.005.359.008.539.01h38.661c5.476-.257 9.707-4.906 9.449-10.382a9.695 9.695 0 0 0-.045-.59v-128c0-6.269-3.657-12.539-9.404-12.539H85.688v-25.6c0-70.692 57.308-128 128-128s128 57.308 128 128v25.6h-11.494c-5.747 0-9.404 6.269-9.404 12.539v128c-.583 5.451 3.363 10.343 8.814 10.926.196.021.393.036.59.045h12.016l-1.045 1.567a82.545 82.545 0 0 1-66.351 32.914c-5.708-27.989-33.026-46.052-61.015-40.343-23.935 4.881-41.192 25.843-41.385 50.27.286 28.65 23.594 51.724 52.245 51.722a53.812 53.812 0 0 0 37.616-16.196 45.978 45.978 0 0 0 12.539-25.078 103.443 103.443 0 0 0 83.069-41.273l9.927-14.629c22.465-1.567 36.571-15.673 36.571-36.049v-65.306c.001-22.463-16.717-49.108-40.75-49.108zM85.688 305.11H58.521c-11.25-.274-20.148-9.615-19.874-20.865.005-.185.012-.37.021-.556v-65.306c0-12.016 8.359-22.988 19.853-22.988h27.167V305.11zm161.437 86.204a30.826 30.826 0 0 1-22.465 9.927c-16.998-.27-30.792-13.834-31.347-30.825-.007-17.024 13.788-30.83 30.812-30.837 17.024-.007 30.83 13.788 30.837 30.812v.025a27.692 27.692 0 0 1-7.837 20.898zm136.359-102.4c0 14.106-13.584 16.196-19.853 16.196h-21.943V195.396h21.943c11.494 0 19.853 16.196 19.853 28.212v65.306z"  opacity="1" data-original="#000000"></path></g></svg>

                    </div>
                    <div class="content">
                        <h2>
                            <a href="tel:{{site_settings('phone')}}">
                                {{site_settings('phone')}}
                            </a>
                        </h2>
                        <p>
                            {{translate('Need Help ? Call Now')}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
