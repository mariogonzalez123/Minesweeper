
function checkPosition(e) {
  if (e.target.nodeName === 'TD' && e.target.childElementCount === 0) { 
    var box = e.target; 
    $.ajax({
      type: 'POST', 
      url: 'index.php', 
      dataType: 'json', 
      data: {
        x: box.dataset.x,
        y: box.dataset.y
      },
      success: function(result) { 
        console.log(result);
        if (result.descubrir !== undefined) {
          for (var i = 0; i < result.descubrir.length; i++) {
            var cell = $(`#${result.descubrir[i][0]}${result.descubrir[i][1]}`);
            cell.css('background-color', 'white');
            switch (result.descubrir[i][2]) {
              case 0:
                cell.html('');
                break;
              case 9:
                cell.html(`<img src="${box.dataset.imgpath}">`);
                break;
              default:
                cell.html(result.descubrir[i][2]);
                if (result.descubrir[i][2] > 0) {
                  cell.css('color', getColor(result.descubrir[i][2]));
                }
            }
          }
        }

        if (result.gameRes !== undefined) {
          switch (result.gameRes) {
            case 1:
              $('#message').text('YOU WIN!!');
              $('#message').css('color', 'blue');
              break;
            case -1:
              $('#message').text('YOU LOSE!!');
              $('#message').css('color', 'red');
              break;
          }
          $('table').unbind('click');
        }
      },
      error: function(xhr, status, error) { 
        var errorMessage = xhr.status + ': ' + xhr.statusText;
        alert('Error - ' + errorMessage); 
      }
    });
  }
}


function getColor(number) {
  switch (number) {
    case 1:
      return 'blue';
    case 2:
      return 'green';
    default:
      return 'red';
  }
}
$(document).ready(function() {
  $('table').click(checkPosition); 
});