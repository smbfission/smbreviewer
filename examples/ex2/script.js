let count = 0;

var loadFile = function() {
  var img = document.getElementById('profilepic');
  var formimg = document.getElementById('formpic');
  img.src = URL.createObjectURL(event.target.files[0]);
  formimg.src = URL.createObjectURL(event.target.files[0]);
  
}
var loadtext = function() {

  var user = document.getElementById('username').value;



  var a = document.getElementById("userpreview");
  a.innerText = user;
  a.style.background = 'none';
  a.style.borderRadius = "0px";
  a.style.borderBottom = "solid 1px";
  a.style.animation = "none";
}
var loadreview = function() {
  var userreview = document.getElementById('userreview').value;
  var b = document.getElementById("userreviewpreview");
  b.innerText = userreview;
  b.style.background = "none";
  b.style.borderRadius = "0px";
  b.style.borderBottom = "solid 1px";
  b.style.animation = "none";
}
var loadmail = function() {
  var useremail = document.getElementById('useremail').value;
  var c = document.getElementById("useremailpreview");
  c.innerText = useremail;
  c.style.background = "none";
  c.style.background = 'none';
  c.style.borderRadius = "0px";
  c.style.borderBottom = "solid 1px";
  c.style.animation = "none";
}
var loadcomment = function() {
  var usercomments = document.getElementById('usercomments').value;

  var d = document.getElementById("usercommentspreview")
  d.innerText = usercomments;
  d.style.background = "none";
  d.style.background = 'none';
  d.style.borderRadius = "0px";
  d.style.borderBottom = "solid 1px";
  d.style.animation = "none";
}

function ones() {

  var ones = document.getElementById('onestar');
  var twos = document.getElementById('twostar');

  var threes = document.getElementById('threestar');
  var fours = document.getElementById('fourstar');
  var fives = document.getElementById('fivestar');
  var ones = document.getElementById('prevonestar');
  var prevones = document.getElementById('prevonestar');
  var prevtwos = document.getElementById('prevtwostar');
  var prevthrees = document.getElementById('prevthreestar');
  var prevfours = document.getElementById('prevfourstar');
  var prevfives = document.getElementById('prevfivestar');
  var ones = document.getElementById('onestar');

  ones.style.color = "orange";
  twos.style.color = "black";
  threes.style.color = "black";
  fours.style.color = "black";
  fives.style.color = "black";
  prevones.style.color = "orange";
  prevtwos.style.color = "black";
  prevthrees.style.color = "black";
  prevfours.style.color = "black";
  prevfives.style.color = "black";
}

function twos() {
  var ones = document.getElementById('onestar');
  var twos = document.getElementById('twostar');

  var threes = document.getElementById('threestar');
  var fours = document.getElementById('fourstar');
  var fives = document.getElementById('fivestar');
  var prevones = document.getElementById('prevonestar');
  var prevtwos = document.getElementById('prevtwostar');
  var prevthrees = document.getElementById('prevthreestar');
  var prevfours = document.getElementById('prevfourstar');
  var prevfives = document.getElementById('prevfivestar');
  ones.style.color = "orange";
  var twos = document.getElementById('twostar');
  twos.style.color = "orange";
  threes.style.color = "black";
  fours.style.color = "black";
  fives.style.color = "black";
  prevones.style.color = "orange";
  prevtwos.style.color = "orange";
  prevthrees.style.color = "black";
  prevfours.style.color = "black";
  prevfives.style.color = "black";
}

function threes() {
  var ones = document.getElementById('onestar');
  var twos = document.getElementById('twostar');

  var threes = document.getElementById('threestar');
  var fours = document.getElementById('fourstar');
  var fives = document.getElementById('fivestar');
  var prevones = document.getElementById('prevonestar');
  var prevtwos = document.getElementById('prevtwostar');
  var prevthrees = document.getElementById('prevthreestar');
  var prevfours = document.getElementById('prevfourstar');
  var prevfives = document.getElementById('prevfivestar');
  ones.style.color = "orange";
  var twos = document.getElementById('twostar');
  twos.style.color = "orange";
  var threes = document.getElementById('threestar');
  threes.style.color = "orange";
  fours.style.color = "black";
  fives.style.color = "black";
  prevones.style.color = "orange";
  prevtwos.style.color = "orange";
  prevthrees.style.color = "orange";
  prevfours.style.color = "black";
  prevfives.style.color = "black";
}

function fours() {
  var ones = document.getElementById('onestar');
  var twos = document.getElementById('twostar');

  var threes = document.getElementById('threestar');
  var fours = document.getElementById('fourstar');
  var fives = document.getElementById('fivestar');
  var ones = document.getElementById('onestar');
  var prevones = document.getElementById('prevonestar');
  var prevtwos = document.getElementById('prevtwostar');
  var prevthrees = document.getElementById('prevthreestar');
  var prevfours = document.getElementById('prevfourstar');
  var prevfives = document.getElementById('prevfivestar');
  ones.style.color = "orange";
  var twos = document.getElementById('twostar');
  twos.style.color = "orange";
  var threes = document.getElementById('threestar');
  threes.style.color = "orange";
  var fours = document.getElementById('fourstar');
  fours.style.color = "orange";
  fives.style.color = "black";
  prevones.style.color = "orange";
  prevtwos.style.color = "orange";
  prevthrees.style.color = "orange";
  prevfours.style.color = "orange";
  prevfives.style.color = "black";
}

function fives() {
  var prevones = document.getElementById('prevonestar');
  var prevtwos = document.getElementById('prevtwostar');
  var prevthrees = document.getElementById('prevthreestar');
  var prevfours = document.getElementById('prevfourstar');
  var prevfives = document.getElementById('prevfivestar');
  var ones = document.getElementById('onestar');
  var twos = document.getElementById('twostar');

  var threes = document.getElementById('threestar');
  var fours = document.getElementById('fourstar');
  var fives = document.getElementById('fivestar');

  ones.style.color = "orange";
  twos.style.color = "orange";

  threes.style.color = "orange";

  fours.style.color = "orange";

  fives.style.color = "orange";
  prevones.style.color = "orange";
  prevtwos.style.color = "orange";
  prevthrees.style.color = "orange";
  prevfours.style.color = "orange";
  prevfives.style.color = "orange";

}
