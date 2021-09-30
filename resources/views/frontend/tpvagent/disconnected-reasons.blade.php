 <!--new-design-start-Declined Lead-options---->
<div class="col-sm-12">
  <div class="declined_lead-wrapper">
    <div class="declined_lead-options">
      <div class="form-group radio-btns pdt0">
        <p style="margin-bottom: 22px;"><strong>Please choose an approprite call disconnected disposition:</strong></p>
        @forelse($dispositions as $disposition)
        <label class="radio-inline radio-outer">
          <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}" style="top: -7px;">
          {{$disposition->description}}
        </label>
        @empty
        <p>No dispositions found.</p>
        @endforelse
        <button type="button" class="btn btn-red mt15" id="hangup_decline" disabled="disabled">Submit</button>
      </div>
    </div>
  </div>
</div>
<!--end-Declined Lead-options---->
