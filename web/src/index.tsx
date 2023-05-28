import { createRoot } from "react-dom/client";
import "./index.css"

document.body.innerHTML = "<div id='app'></div>";

const root = createRoot(document.getElementById("app") as HTMLDivElement);
root.render(<h1>Heavyrain Console</h1>);
