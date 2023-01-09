@extends('layouts.app', [
    'title' => 'Frequently Asked Questions (FAQ)',
    'description' => 'Check out Tell Molly using the demo account. Find out if it is right for you. '
])

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">Frequently Asked Questions (FAQ)</h1>

                <div class="accordion mt-3" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Who is Tell Molly for?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <strong>Everyone.</strong> Journaling can help to improve your mental health. Write it down instead of letting it get you down. Share happy moments and
                                rediscover them later. Share what you are grateful for each day. Having a positive mindset can dramatically improve how you approach each day.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Are my journal entries secure?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <strong>Yes.</strong> Tell Molly uses state-of-the-art encryption. Your data is securely sent from your browser to our server leaving no way for anyone to inspect your entries.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseTwo">
                                Do you share my data with third-parties?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <strong>No.</strong> We do not share your data with third-parties. Tell Molly does not use any external services to make sure your data stays secure.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                How do I use Tell Molly correctly?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <strong>It's up to you.</strong> There is no right or wrong way of using Tell Molly. However, to get the most out of your experience
                                we suggest you customize your tags.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
