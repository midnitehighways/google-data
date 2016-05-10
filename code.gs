function myFunction() {
  var ss = SpreadsheetApp.openByUrl(
     'https://docs.google.com/spreadsheets/d/11kI3ihoDbGsrVSOVfr1UMCQr7k3TE0a-oOlaCrtFYlE/edit#gid=0');
  Logger.log(ss.getName());
  var first = ss.getSheetByName("Drive");
  first.clearContents();
  
  
}
function doGet() {
//  return HtmlService.createHtmlOutputFromFile('Index')
//      .setSandboxMode(HtmlService.SandboxMode.IFRAME);
  myFunction();
  return null;
}