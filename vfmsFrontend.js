function post(uri, postData, method='post') {
  
  let form = document.createElement('form');
  form.method = method;
  form.action = uri;
  form.target = "_blank";

  for (var key in postData) {
    if (postData.hasOwnProperty(key)) {
      let field = document.createElement('input');
      field.type = 'hidden';
      field.name = key;
      field.value = postData[key];

      form.appendChild(field);
    }
  }

  document.body.appendChild(form);
  form.submit();
}

function vfmsUse(obj) {
    let type=obj.getAttribute("vfmsAction");
    let fileid=obj.getAttribute("fid");

    if (type=="view") {
        post('/viewfile.php', {"fid": fileid});
    } else if (type=="edit") {
      let inp = prompt("Enter the three-digit permission.");
      numVal = parseInt(inp,8);
      if (numVal && numVal>=0 && numVal<512) {
        $.post( "perms.php", { "fid": fileid, "octal": numVal } ).done(function( data ) {
          alert( data );
          location.reload();
        });
      } else {
        alert("Please enter a valid three-digit octal.");
      }
    } else if (type=="delete") {
      $.post( "delete.php", { "fid": fileid } ).done(function( data ) {
        alert( data );
        location.reload();
      });
    } else {
        alert(type + " " + fileid);
    }
}