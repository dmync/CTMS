(function(){
  const saved = localStorage.getItem("ctms_theme");
  if(saved){
    document.documentElement.setAttribute("data-theme", saved);
  }

  window.toggleTheme = function(){
    const cur = document.documentElement.getAttribute("data-theme");
    const next = (cur === "dark") ? "light" : "dark";
    document.documentElement.setAttribute("data-theme", next);
    localStorage.setItem("ctms_theme", next);
  }
})();
