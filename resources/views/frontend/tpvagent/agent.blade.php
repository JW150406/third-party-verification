@extends('layouts.app')
@section('content')
<!DOCTYPE html>
<html>
<head>
    <title>Customer Care - Voice Agent Screen</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
        integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//media.twiliocdn.com/taskrouter/quickstart/agent.css"/>
    <script src="{{ asset('js/taskrouter.min.js') }}"></script>
    <script src="{{ asset('js/twilio.min.js') }}"></script>
    <script src="{{ asset('js/agent.js') }}?v=9"></script>
    <script src="{{ asset('js/main.js') }}?v=3"></script>
    <style>
    p.activity {
      width : 100%;
      display : table;
      margin : 30px 0 10px 0;
    }
    </style>
</head>
<body>

<div class="col-md-6 col-md-offset-3">
          <div class="panel panel-info client-controls" style="display:none;">

            <!-- <div class="panel-heading">
              <h3 class="panel-title">Support Agent Client</h3>
            </div> -->

            <div class="panel-body">
              <!-- <p style=""><strong>Status</strong></p>
              <div class="well well-sm" id="call-status">
                Waiting for user to connect as an agent...
              </div> -->

              <!-- <div class="row" id="connect-agent-row hidden">
                <div class="col-md-6 text-center">
                  <button id="connect-agent1-button" class="btn btn-lg btn-primary" data-rel="{{$workerSid}}">
                    Connect as Agent 1
                  </button>
                </div>
                <div class="col-md-6 text-center">
                  <button id="connect-agent2-button" class="btn btn-lg btn-info">
                    Connect as Agent 2
                  </button>
                </div>
              </div> -->

              <div class="row hidden" id="connected-agent-row">
                <div class="col-md-4 text-center">
                  {{-- <button id="answer-call-button" class="btn btn-lg btn-success" disabled>
                    Awswer call
                  </button> --}}
                </div>
                
                <div class="col-md-4 text-center">
                  <button id="hangup-call-button" class="btn btn-lg btn-danger" disabled>
                    Hangup
                  </button>
                </div>
              </div>
              <div class="row hidden" id="complete-task-row">
                 <div class="col-md-4 text-center">
                     <button id="complete-task-button" class="btn btn-lg btn-success">
                    Complete
                    </button>
                </div>
              </div>

            </div>

          </div>
        </div>
        <div class="clearfix"></div>
<div class="content">
    <section class="agent-activity offline">
        <p class="activity">Offline</p>
        <button class="change-activity" data-next-activity="Idle">Go Available</button>
    </section>
    <section class="agent-activity idle">
        <p class="activity"><span>Available</span></p>
        <button class="change-activity" data-next-activity="Offline">Go Offline</button>
    </section>
    <section class="agent-activity reserved">
        <p class="activity">Reserved</p>
    </section>
    <section class="agent-activity busy">
        <p class="activity">Busy</p>
      </section>
    <section class="agent-activity wrapup">
        <p class="activity">Wrap-Up</p>
        <button class="change-activity" data-next-activity="Idle">Go Available</button>
        <button class="change-activity" data-next-activity="Offline">Go Offline</button>
    </section>
    <section class="log">
      <textarea id="log" readonly="true"></textarea>
    </section>
</div>
<script>
  window.workerToken = "<?= $workerToken ?>";
  window.clientToken = "<?= $token ?>";
  window.workerSid = "<?= $workerSid   ?>";
  //fetchToken("<?= $workerSid ?>");
</script>
</body>
@endsection
