const signInButton = document.getElementById("signInButton");
const signUpButton = document.getElementById("signUpButton");
const signInForm = document.getElementById("signin");
const signUpForm = document.getElementById("signup");

signUpButton.addEventListener("click",function(){
    signInForm.style.display="none";
    signUpForm.style.display="block";
});

signInButton.addEventListener("click",function(){
    signInForm.style.display="block";
    signUpForm.style.display="none";
    
});
