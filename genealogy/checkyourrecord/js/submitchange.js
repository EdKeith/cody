/* Tally up the changed fields   */
function compileChanges(uniqueid) {
  // Check for required fields
  
  
  
  
  submitterdata = {
    "FirstName": $("#submitter-first-name" + uniqueid).val().trim(),
    "LastName": $("#submitter-last-name" + uniqueid).val().trim(),
    "CodyId": $("#submitter-cody-id" + uniqueid).val().trim(),
    "Email": $("#submitter-email" + uniqueid).val().trim(),
    "Comment": $("#comment" + uniqueid).val().trim()
  };

  if (submitterdata.FirstName.length == 0 ||
    submitterdata.LastName.length == 0 ||
    isEmail(submitterdata.Email) == false) {

    $("#must-include-message" + uniqueid).css('color', 'red');
    //alert("Please include first name, last name and a proper email address");
    return false;
  }
  sessionStorage.setItem('submitter', JSON.stringify(submitterdata));

  // console.log('$("#edit-window" + uniqueid).data() = ', $("#edit-window" + uniqueid).data());
  // Saved original data sent to populate this form

  // deep copy so changes made here will not effect the original
  var originalData = JSON.parse(JSON.stringify($("#edit-window" + uniqueid).data()));
  console.log("originalData = ", originalData);

  // get form data
  // var id = $("#edit-form" + uniqueid).prop('id');
  var $form = $("#edit-form" + uniqueid);
  console.log("$form = ", $form);

  var serializedFormData = $form.serialize(); // Serialize the form inputs
  console.log("serializedFormData = ", serializedFormData);
  var submittedInputs = serializedFormData.split('&');
  var submittedData = {};
  var numberOfInputs = 0;

  console.log("submittedInputs = ", submittedInputs);
  $.each(submittedInputs, function(index, value) {

    console.log("value = ", value, " index = ", index);
    var nameandvalue = value.split('=');
    var inputName = nameandvalue[0].replace(uniqueid, ""); // remove id decoration
    var inputValue = nameandvalue[1];

    console.log("inputName = ", inputName);
    console.log("inputValue = ", inputValue);
    /* divorced */
    if (inputName.indexOf("divorce") > 0) {
      inputValue = "div";
    }
    if (inputName.indexOf("submitter") == -1 && inputName.indexOf("comment") == -1) {
      submittedData[inputName] = inputValue.replace(/\+/g, " ");
      console.log("submittedData = ", submittedData);
      numberOfInputs++;
    }
  });
  console.log("numberOfInputs = ", numberOfInputs)
  console.log("final value of submittedData = ", submittedData);

  displayModalWindow(uniqueid, originalData, submittedData, submitterdata);
}

function displayModalWindow(uniqueid, originalData, submittedData, submitterdata) {
  //**************************************************************//
  // CHECK FOR CHANGED FIELDS
  //**************************************************************//
  var changedFields = {};
  var numberOfChanges = 0;
  $.each(submittedData, function(submitIndex, submitValue) {
    //console.log(submitIndex+ " "+ submitValue);
    console.log("submit Index, value  = ( ", submitIndex, " , ", submitValue, ")");
    var submitLabel = submitIndex.replace(/\-/g, " ");
    submitLabel = submitLabel.charAt(0).toUpperCase() + submitLabel.slice(1);
    var submitValueString = String(submitValue);
    var submitValueCleaned = submitValueString.replace(/\+/g, ' ');

    var $changeRow;
    if (submitIndex in originalData) {
      var originalValue = originalData[submitIndex];
      if (submitValueCleaned != originalValue) {
        changedFields[submitIndex] = submitValueCleaned;
        $changeRow = $("<tr class='confirm-data-row'><td class='label-cell'>" + submitLabel +
          "</td><td class='original-data-cell'>" + originalValue +
          "</td><td class='submitted-data-cell'>" + submitValueCleaned + "</td></tr>");
        // $("#confirm-table").append($changeRow);
        $("#submit-confirm-table-body").append($changeRow);
        numberOfChanges++;
      }
    } else {
      if (submitValueCleaned.length > 0) {
        $changeRow = $("<tr class='confirm-data-row'><td class='label-cell'>" + submitLabel +
          "</td><td class='original-data-cell'></td><td class='submitted-data-cell'>" +
          submitValueCleaned + "</td></tr>");
        $("#confirm-table").append($changeRow);
        numberOfChanges++;
      }
    }
  });
  //**************************************************************//
  // If there are valid changes, show CONFIRM OVERLAY
  //**************************************************************//
  if (numberOfChanges > 0) {
    // Saved original data sent to populate this form
    // var originalData = JSON.parse(JSON.stringify($("#edit-window" + uniqueid).data()));

    var confirmTitle = "Suggested Corrections for Cody ID " + $("#codyidspan" + uniqueid).html();
    $("#confirm-title").html(confirmTitle);
    $("#confirm-first-name").html(submitterdata.FirstName);
    $("#confirm-last-name").html(submitterdata.LastName);
    $("#confirm-cody-id").html(submitterdata.CodyId);
    $("#confirm-email").html(submitterdata.Email);
    if (submitterdata.Comment.length > 0) {
      $("#confirm-comment").html(submitterdata.Comment);
      $("#comment" + uniqueid).val('')
      $("#confirm-comment-container").show();
    }
    $("#unique-id").html(uniqueid);
    $("#original-data").data(originalData);
    $("#submitted-data").data(submittedData);
    $("#submit-confirm-overlay").show();
  } else {
    alert("No changes were made");
  }
}

function onClickSubmitConfirm(event) {
  var uniqueid = $("#unique-id").html();

  // make a deep copy
  var originalData = JSON.parse(JSON.stringify($("#original-data").data()));
  var submittedData = $("#submitted-data").data();
  console.log("submittedData = ", submittedData);
  var newRecordLine = "";
  var confirmFirstName = $("#confirm-first-name").html();
  var confirmLastName = $("#confirm-last-name").html();
  var confirmCodyId = $("#confirm-cody-id").html();
  var confirmEmail = $("#confirm-email").html();
  var confirmComment = $("#confirm-comment").html();
  $("#confirm-comment").html('');
  var mailText = $("#confirm-title").html() + "\n\r";
  mailText += $("#submitter-info").text() + "\n\r";

  var senderdata = {
    'lastname': confirmFirstName,
    'firstname': confirmFirstName,
    'codyid': confirmCodyId,
    'email': confirmEmail
  };
  console.log('sender data: ', senderdata);
  $("#family-info").data('sender', senderdata);


  $("#confirm-table tr.confirm-data-row").each(function() {
    // var $tr = $(this);
    var label = $(this).find("td.label-cell").html();
    var originalDataCell = $(this).find("td.original-data-cell").html();
    var submittedDataCell = $(this).find("td.submitted-data-cell").html();
    var line = label + ":  Change " + originalDataCell + " to " + submittedDataCell + "\n";
    mailText += line;
  });
  mailText += "\n\rComment:  " + confirmComment;
  mailText += "\n\rOriginal Text Record:\n\r" + originalData['line'] + "\n\r";
  $("#submit-confirm-overlay").hide();
  $("#confirm-table tr.confirm-data-row").remove();

  /* var */
  newRecordLine = constructNewRecordLine(originalData, submittedData);
  mailText += "Updated Text Record:\n\r" + newRecordLine + "\n\r";
  console.log(mailText);
  // SUBMIT CONFIRM
  submitConfirm(uniqueid, confirmFirstName, confirmLastName, confirmCodyId, confirmEmail, mailText);
}


//**************************************************************//
// SEND MAIL
//**************************************************************//
function submitConfirm(uniqueid, firstname, lastname, codyid, email, suggestions, newRecordLine) {
  $.ajax({
      url: '../cfo/pfd/swi.php',
      type: "POST",
      data: {
        action: 'submitsuggestion',
        firstname: firstname,
        lastname: lastname,
        codyid: codyid,
        email: email,
        suggestions: suggestions,
        newrecordline: newRecordLine
      }
    })
    .done(function(data) {
      $("#edit-window" + uniqueid).remove();
      alert("Thank you for your suggestions, they will be review and, if valid, included in our next Genealogy.");
      var submitmessage = "Suggested corrections submitted by " + firstname + " " + lastname + " - Thank you!";
      $("#searchbyname-message").html(submitmessage);

      location.reload(); //*TODO: reload page to clear data. A better solution should be found.
    })
    .fail(function(jqXHR, textStatus, errorThrown) {

    })
}


function initSubmitCorection() {
  /**************************************************************/
  // Click events for SUBMIT CONFIRM OVERLAY
  //**************************************************************/
  $("#cancelconfirm").click(function(event) {
    $("#submit-confirm-overlay").hide();
    $("#confirm-table tr.confirm-data-row").remove();
  });

  $("#submitconfirm").click(onClickSubmitConfirm);
}