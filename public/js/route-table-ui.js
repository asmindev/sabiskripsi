// Route Table UI Module
(function () {
    "use strict";

    // Display route table
    function displayRouteTable(routes, locations, userRole = "pengguna") {
        const routeResults = document.getElementById("routeResults");
        routeResults.innerHTML = "";

        routes.forEach((route, index) => {
            const routeCard = createRouteCard(route, index, locations);
            routeResults.appendChild(routeCard);
        });

        setupEventListeners(routes, locations);
    }

    // Create route card
    function createRouteCard(route, index, locations) {
        const routeCard = document.createElement("div");
        routeCard.className =
            "bg-gray-50 rounded-lg p-4 border-l-4 mb-2 border-green-500";

        const getProgressPercent = () => {
            const tpsList = route.path.filter((p) => p !== "depot");
            const done = tpsList.filter(
                (p) => route.tpsStatus[p] === "sudah"
            ).length;
            return tpsList.length > 0
                ? Math.round((done / tpsList.length) * 100)
                : 0;
        };

        routeCard.innerHTML = `
        <div class="flex items-center justify-between mb-2">
            <h4 class="font-semibold text-gray-900">${route.vehicle}</h4>
            <span class="text-sm text-gray-600">${
                route.path.filter((p) => p !== "depot").length
            } TPS</span>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
            <div class="progressBar bg-green-500 h-3 rounded-full" style="width: ${getProgressPercent()}%;"></div>
        </div>
        <div class="text-xs text-gray-600 mb-2 progressText">
            <span class="font-medium">Progres:</span> ${getProgressPercent()}% selesai
        </div>

        <div class="text-xs text-gray-600 mb-2">
            <span class="font-medium">Rute TPS:</span>
        </div>
        <div class="mb-2 tpsList">
            ${generateTPSList(route, locations)}
        </div>

        <div class="mb-2">
            <label class="text-gray-600 text-sm">Pilih TPS:</label>
            <select class="tpsDropdown border rounded p-1 text-sm w-full" data-index="${index}">
                <option value="">Pilih TPS</option>
                ${generateTPSOptions(route, locations)}
            </select>
        </div>

        <div class="mb-2">
            <label class="text-gray-600 text-sm">Status:</label>
            <select class="statusDropdown border rounded p-1 text-sm w-full" data-index="${index}">
                <option value="">Pilih Status</option>
                <option value="sudah di angkut">Sudah Di Angkut</option>
                <option value="belum di angkut">Belum Di Angkut</option>
            </select>
        </div>

        <button class="saveStatusBtn mt-1 bg-green-500 text-white text-sm px-2 py-1 rounded hover:bg-green-600" data-index="${index}">
            Simpan Status
        </button>
    `;

        return routeCard;
    }

    // Generate TPS list
    function generateTPSList(route, locations) {
        return route.path
            .filter((p) => p !== "depot")
            .map((p, idx) => {
                const tpsLoc = window.VRPMap?.findLocation(p, locations);
                const statusClass =
                    route.tpsStatus[p] === "sudah"
                        ? "text-green-600 font-medium"
                        : "text-gray-600";
                return `
            <div class="flex items-center gap-2 mb-1" data-tps="${p}">
                <span class="font-medium">${idx + 1}.</span>
                <span class="${statusClass}">
                    ${tpsLoc?.name || p}
                </span>
            </div>
        `;
            })
            .join("");
    }

    // Generate TPS options for dropdown
    function generateTPSOptions(route, locations) {
        return route.path
            .filter((p) => p !== "depot")
            .map((p) => {
                const tpsLoc = window.VRPMap?.findLocation(p, locations);
                return `<option value="${p}">${tpsLoc?.name || p}</option>`;
            })
            .join("");
    }

    // Setup event listeners
    function setupEventListeners(routes, locations) {
        const routeResults = document.getElementById("routeResults");

        routeResults.addEventListener("click", (e) => {
            if (e.target.classList.contains("saveStatusBtn")) {
                handleStatusSave(e, routes, locations);
            }
        });
    }

    // Handle status save
    function handleStatusSave(e, routes, locations) {
        const index = e.target.dataset.index;
        const route = routes[index];
        const routeResults = document.getElementById("routeResults");
        const card = routeResults.children[index];

        const progressBar = card.querySelector(".progressBar");
        const progressText = card.querySelector(".progressText");
        const tpsListDiv = card.querySelector(".tpsList");
        const tpsDropdown = card.querySelector(".tpsDropdown");
        const statusDropdown = card.querySelector(".statusDropdown");

        const tps = tpsDropdown.value;
        const status = statusDropdown.value;

        if (tps && status) {
            const normalizedStatus = status.includes("sudah")
                ? "sudah"
                : "belum";

            // Update via VRPAlgorithm module
            if (window.VRPAlgorithm) {
                window.VRPAlgorithm.updateStatus(index, tps, status);
            }

            route.tpsStatus[tps] = normalizedStatus;

            // Update tampilan TPS di list
            const tpsDiv = tpsListDiv.querySelector(
                `div[data-tps="${tps}"] span:nth-child(2)`
            );
            tpsDiv.className =
                normalizedStatus === "sudah"
                    ? "text-green-600 font-medium"
                    : "text-gray-600";

            // Update progress bar
            const tpsElements = route.path.filter((p) => p !== "depot");
            const done = tpsElements.filter(
                (p) => route.tpsStatus[p] === "sudah"
            ).length;
            const progressPercent = Math.round(
                (done / tpsElements.length) * 100
            );
            progressBar.style.width = `${progressPercent}%`;
            progressText.innerHTML = `<span class="font-medium">Progres:</span> ${progressPercent}% selesai`;

            // Add notification
            const tpsLoc = window.VRPMap?.findLocation(tps, locations);
            const tpsName = tpsLoc?.name || tps;
            const message = `✅ ${route.vehicle}: TPS "${tpsName}" ${
                normalizedStatus === "sudah"
                    ? "sudah di angkut"
                    : "belum di angkut"
            }`;

            if (window.NotificationSystem) {
                window.NotificationSystem.add(message);
            }
        }
    }

    // Export route table functions
    window.RouteTableUI = {
        display: displayRouteTable,
    };
})();
