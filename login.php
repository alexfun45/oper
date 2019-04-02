<link href="./src/scss/login.css" rel="stylesheet" type="text/css"></link>
<h1>LogIn</h1>
<div id="wrapper">
	<form id="signin" method="POST" action="./" autocomplete="off">
		<input type="text" id="user" name="login" placeholder="username" />
		<input type="password" id="pass" name="pass" placeholder="password" />
		<button type="submit">&#xf0da;</button>
		<input type="hidden" name='oper' value='login'>
		<!--<p>forgot your password? <a href="#">click here</a></p>-->
	</form>
</div>