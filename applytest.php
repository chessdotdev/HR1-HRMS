<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Apply</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  .d-none { display: none !important; }
</style>
</head>
<body>

<div class="container py-4" style="max-width: 720px;">
  <div class="page-title text-center mb-4">
    <h1 class="apply-title">Apply Now</h1>
    <p class="page-subtitle mx-auto" style="max-width: 420px;">
      Join our team and build a rewarding career in hospitality. Fill out the form below to get started.
    </p>
  </div>

  <form id="applyForm" class="form-card p-4 sm-5 formCard">
    <!-- Step 1: Personal Info -->
    <div class="form-step" id="step-0">
      <h2 class="section-title heading-font">Personal Information</h2>
      <p class="section-desc">Please provide your basic personal details.</p>
      <div class="row g-3">
        <div class="col-sm-6">
          <label class="form-label" for="firstName">First Name <span class="required">*</span></label>
          <input type="text" class="form-control" id="firstName" placeholder="Juan" />
          <div class="invalid-feedback">First name is required.</div>
        </div>
        <div class="col-sm-6">
          <label class="form-label" for="lastName">Last Name <span class="required">*</span></label>
          <input type="text" class="form-control" id="lastName" placeholder="Dela Cruz" />
          <div class="invalid-feedback">Last name is required.</div>
        </div>
      </div>
      <div class="row g-3 mt-1">
        <div class="col-sm-6">
          <label class="form-label" for="middleName">Middle Name</label>
          <input type="text" class="form-control" id="middleName" placeholder="Santos" />
        </div>
        <div class="col-sm-6">
          <label class="form-label" for="suffix">Suffix</label>
          <select class="form-select" id="suffix">
            <option value="">Select suffix</option>
            <option value="none">None</option>
            <option value="jr">Jr.</option>
            <option value="sr">Sr.</option>
            <option value="ii">II</option>
            <option value="iii">III</option>
            <option value="iv">IV</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Step 2: Example Job Info -->
    <div class="form-step d-none" id="step-1">
      <h2 class="section-title heading-font">Job Details</h2>
      <div class="mb-3">
        <label class="form-label" for="positionApplied">Position Applied <span class="required">*</span></label>
        <input type="text" class="form-control" id="positionApplied" placeholder="e.g. Commis Chef" />
        <div class="invalid-feedback">Position is required.</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="department">Department <span class="required">*</span></label>
        <input type="text" class="form-control" id="department" placeholder="e.g. Kitchen" />
        <div class="invalid-feedback">Department is required.</div>
      </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex justify-content-between mt-4">
      <button type="button" id="prevBtn" class="btn btn-secondary" disabled>Previous</button>
      <button type="button" id="nextBtn" class="btn btn-primary">Next</button>
    </div>
  </form>

  <!-- Success Message -->
  <div id="successState" class="d-none mt-4 text-center">
    <h3>Application Submitted!</h3>
    <p>Thank you for applying. Our HR team will contact you soon.</p>
  </div>

</div>

<script>
(function(){
  'use strict';

  const steps = document.querySelectorAll('.form-step');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const successState = document.getElementById('successState');
  let currentStep = 0;

  const val = id => document.getElementById(id)?.value.trim() || '';
  const setInvalid = id => document.getElementById(id)?.classList.add('is-invalid');
  const clearValidation = () => document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

  function showStep(step){
    steps.forEach((s,i)=> s.classList.toggle('d-none', i !== step));
    prevBtn.disabled = step === 0;
    nextBtn.textContent = step === steps.length -1 ? 'Submit' : 'Next';
  }

  function validateStep(step){
    clearValidation();
    let valid = true;
    if(step === 0){
      if(!val('firstName')) setInvalid('firstName') || (valid=false);
      if(!val('lastName')) setInvalid('lastName') || (valid=false);
    }
    if(step === 1){
      if(!val('positionApplied')) setInvalid('positionApplied') || (valid=false);
      if(!val('department')) setInvalid('department') || (valid=false);
    }
    return valid;
  }

  nextBtn.addEventListener('click', ()=>{
    if(!validateStep(currentStep)) return;
    if(currentStep === steps.length -1){
      document.getElementById('applyForm').classList.add('d-none');
      successState.classList.remove('d-none');
      return;
    }
    currentStep++;
    showStep(currentStep);
    window.scrollTo({top:0, behavior:'smooth'});
  });

  prevBtn.addEventListener('click', ()=>{
    if(currentStep===0) return;
    currentStep--;
    showStep(currentStep);
    window.scrollTo({top:0, behavior:'smooth'});
  });

  showStep(currentStep);

})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
