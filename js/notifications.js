let bell = document.querySelector("#bell");
let notifications = document.querySelector("#notifications");
let style = notifications.style;

let interval;
let timeout;

bell.addEventListener("mouseenter", () => {
    clearInterval(interval);
    clearTimeout(timeout);
    style.opacity = 1;
    style.display = "flex";
});

bell.addEventListener("mouseleave", () => {
    timeout = setTimeout(() => {
        style.opacity = 1;
        clearInterval(interval);
        interval = setInterval(() => {
            style.opacity -= 0.1;
            if (style.opacity < 0) {
                clearInterval(interval);
                style.display = "none";
            }
        }, 10);
    }, 300);
});