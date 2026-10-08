window.addEventListener("DOMContentLoaded", () => {
    const flowCanvas = document.getElementById("flowCanvas");
    const previewPanel = document.getElementById("previewPanel");
    const previewBtn = document.querySelector(".btn.secondary");
    const closePreview = document.getElementById("closePreview");
    const settingsForm = document.getElementById("node-settings-form");

    let nodesData = {}; // Store node configs
    let selectedNode = null;
    let selectedNodeEl = null;
    let offsetX = 0;
    let offsetY = 0;
    let isPreviewOpen = false;

    // Initialize Global Settings Listeners
    if (document.getElementById('bot-name')) {
        document.getElementById('bot-name').addEventListener('input', (e) => window.LaravelData.chatbotName = e.target.value);
        document.getElementById('trigger-type').addEventListener('change', (e) => window.LaravelData.triggerType = e.target.value);
        document.getElementById('trigger-value').addEventListener('input', (e) => window.LaravelData.triggerValue = e.target.value);
        document.getElementById('set-is-active').addEventListener('change', (e) => window.LaravelData.isActive = e.target.checked);
        if(document.getElementById('whatsapp-project-id')) {
            document.getElementById('whatsapp-project-id').addEventListener('change', (e) => window.LaravelData.whatsappProjectId = e.target.value);
        }
    }

    // Default configuration for new nodes
    const getDefaultConfig = (type) => {
        if (type === 'message') return { text: "This is a message." };
        if (type === 'button') return { text: "Please select an option", buttons: ["Option 1", "Option 2"] };
        if (type === 'question') return { text: "What is your name?" };
        if (type === 'start') return { text: "Welcome to our Chatbot!" };
        return {};
    };

    /* ================= DRAG MOVE NODES ================= */
    function makeDraggable(nodeEl) {
        nodeEl.addEventListener("mousedown", (e) => {
            if (isPreviewOpen) return;
            if (e.target.tagName === 'I' || e.target.closest('.delete-node')) return; // Ignore delete clicks
            
            selectedNodeEl = nodeEl;
            offsetX = e.clientX - nodeEl.offsetLeft;
            offsetY = e.clientY - nodeEl.offsetTop;
            nodeEl.style.zIndex = 999;
            
            selectNode(nodeEl.id);
        });

        nodeEl.addEventListener("dblclick", (e) => {
            if (isPreviewOpen) return;
            selectNode(nodeEl.id);
            renderSettings();
        });
    }

    document.addEventListener("mousemove", (e) => {
        if (!selectedNodeEl || isPreviewOpen) return;
        let x = e.clientX - offsetX;
        let y = e.clientY - offsetY;
        x = Math.max(0, Math.min(x, 2800));
        y = Math.max(0, Math.min(y, 1800));
        selectedNodeEl.style.left = x + "px";
        selectedNodeEl.style.top = y + "px";
        
        if (nodesData[selectedNodeEl.id]) {
            nodesData[selectedNodeEl.id].x = x;
            nodesData[selectedNodeEl.id].y = y;
        }

        jsPlumb.repaintEverything();
    });

    document.addEventListener("mouseup", () => {
        if (selectedNodeEl) selectedNodeEl.style.zIndex = 1;
        selectedNodeEl = null;
    });

    /* ================= NODE SELECTION & SETTINGS ================= */
    function selectNode(id) {
        // Remove highlight from previous
        document.querySelectorAll('.flow-node').forEach(el => el.classList.remove('selected'));
        const el = document.getElementById(id);
        if(el) el.classList.add('selected');
        
        selectedNode = id;
    }

    function renderSettings() {
        if (!selectedNode || !nodesData[selectedNode]) {
            return;
        }

        document.getElementById("nodeSettingsModal").style.display = 'flex';
        document.getElementById("settings-title").innerText = "Node Settings";
        const data = nodesData[selectedNode];
        let html = '';

        if (['message', 'start', 'question', 'button'].includes(data.type)) {
            html += `
                <div class="setting-group">
                    <label>Message Text</label>
                    <textarea rows="6" id="setting-text" class="form-control" style="width:100%; padding:10px;" onkeyup="updateNodeData('text', this.value)">${data.config.text || ''}</textarea>
                </div>
            `;
        }

        if (data.type === 'question') {
            html += `
                <div class="setting-group">
                    <label>Save Answer To Variable</label>
                    <input type="text" id="setting-variable" class="form-control" placeholder="e.g. name, email" style="width:100%; padding:10px;" value="${data.config.variable || ''}" onkeyup="updateNodeData('variable', this.value)">
                    <small style="color: #64748b;">The user's reply will be saved under this variable in the session.</small>
                </div>
            `;
        }

        if (data.type === 'condition') {
            html += `
                <div class="setting-group">
                    <label>Variable to Check</label>
                    <input type="text" class="form-control" placeholder="e.g. name" style="width:100%; padding:10px;" value="${data.config.variable || ''}" onkeyup="updateNodeData('variable', this.value)">
                </div>
                <div class="setting-group">
                    <label>Operator</label>
                    <select class="form-control" style="width:100%; padding:10px;" onchange="updateNodeData('operator', this.value)">
                        <option value="==" ${data.config.operator === '==' ? 'selected' : ''}>Equals (==)</option>
                        <option value="!=" ${data.config.operator === '!=' ? 'selected' : ''}>Not Equals (!=)</option>
                        <option value=">" ${data.config.operator === '>' ? 'selected' : ''}>Greater Than (>)</option>
                        <option value="<" ${data.config.operator === '<' ? 'selected' : ''}>Less Than (<)</option>
                        <option value="contains" ${data.config.operator === 'contains' ? 'selected' : ''}>Contains</option>
                    </select>
                </div>
                <div class="setting-group">
                    <label>Value to Match</label>
                    <input type="text" class="form-control" placeholder="e.g. VIP" style="width:100%; padding:10px;" value="${data.config.value || ''}" onkeyup="updateNodeData('value', this.value)">
                </div>
                <div style="padding: 10px; background: #f1f5f9; border-radius: 8px; font-size: 12px; color: #475569;">
                    <b>Routing:</b> Drag from the <span style="color:#10b981">Green (True)</span> or <span style="color:#ef4444">Red (False)</span> dots below this node to route based on the condition.
                </div>
            `;
        }

        if (data.type === 'button') {
            html += `<div class="setting-group"><label>Interactive Buttons (Max 3)</label><div id="buttons-list">`;
            const buttons = data.config.buttons || [];
            buttons.forEach((btn, idx) => {
                html += `
                    <div style="display:flex; gap:5px; margin-bottom:5px;">
                        <input type="text" value="${btn}" onchange="updateButton(${idx}, this.value)" style="flex:1; padding:5px;">
                        <button class="btn btn-sm btn-danger" onclick="removeButton(${idx})"><i class="fa-solid fa-trash"></i></button>
                    </div>
                `;
            });
            if (buttons.length < 3) {
                html += `<button class="btn btn-sm btn-success mt-2" onclick="addButton()">+ Add Button</button>`;
            }
            html += `</div></div>`;
        }

        settingsForm.innerHTML = html;
    }

    window.updateNodeData = (key, value) => {
        if(selectedNode && nodesData[selectedNode]) {
            const data = nodesData[selectedNode];
            data.config[key] = value;
            
            // Update UI preview text
            const p = document.querySelector(`#${selectedNode} .node-body p`);
            if (p) {
                if (data.type === 'condition') {
                    const v = data.config.variable || '?';
                    const o = data.config.operator || '==';
                    const val = data.config.value || '?';
                    p.innerText = `${v} ${o} ${val}`;
                } else if (data.type === 'question') {
                    p.innerText = data.config.text ? data.config.text.substring(0, 30) : 'Question Node';
                } else {
                    if (key === 'text') {
                        p.innerText = value.substring(0, 30) + (value.length > 30 ? '...' : '');
                    }
                }
            }
        }
    };

    window.updateButton = (idx, value) => {
        if(selectedNode && nodesData[selectedNode]) {
            nodesData[selectedNode].config.buttons[idx] = value;
        }
    };

    window.addButton = () => {
        if(selectedNode && nodesData[selectedNode]) {
            if(!nodesData[selectedNode].config.buttons) nodesData[selectedNode].config.buttons = [];
            nodesData[selectedNode].config.buttons.push("New Option");
            renderSettings();
        }
    };

    window.removeButton = (idx) => {
        if(selectedNode && nodesData[selectedNode]) {
            nodesData[selectedNode].config.buttons.splice(idx, 1);
            renderSettings();
        }
    };

    window.deleteNode = (id) => {
        if(id === 'startNode') {
            alert('Cannot delete Start Node');
            return;
        }
        jsPlumb.remove(id);
        delete nodesData[id];
        if(selectedNode === id) {
            selectedNode = null;
            renderSettings();
        }
    };

    /* ================= PREVIEW PANEL ================= */
    const previewBtnHeader = document.getElementById("preview-btn");
    if(previewBtnHeader) {
        previewBtnHeader.addEventListener("click", () => {
            const panel = document.getElementById('previewPanel');
            if (panel.style.display === "none" || panel.style.display === "") {
                panel.style.display = "flex";
                isPreviewOpen = true;
                runPreview();
            } else {
                panel.style.display = "none";
                isPreviewOpen = false;
            }
        });
    }

    closePreview.addEventListener("click", () => {
        document.getElementById('previewPanel').style.display = "none";
        isPreviewOpen = false;
    });

    window.previewVariables = {};

    function runPreview() {
        window.previewVariables = {};
        const preview = document.getElementById("preview-chat");
        preview.innerHTML = '';
        
        let currentNodeId = 'startNode';
        renderPreviewNode(currentNodeId);
    }

    window.submitPreviewAnswer = () => {
        const inputField = document.getElementById("preview-input-field");
        const val = inputField.value.trim();
        if(!val) return;
        
        const preview = document.getElementById("preview-chat");
        preview.innerHTML += `
            <div class="user-message" style="margin-top:10px;">
                ${val}
            </div>
        `;
        
        const waitingNodeId = window.previewWaitingNodeId;
        const waitingVar = window.previewWaitingVariable;
        if(waitingNodeId && waitingVar) {
            window.previewVariables[waitingVar] = val;
        }
        
        inputField.value = '';
        preview.parentElement.scrollTop = preview.parentElement.scrollHeight;
        
        if (waitingNodeId) {
            const edges = jsPlumb.getConnections({ source: waitingNodeId });
            if (edges.length > 0) {
                setTimeout(() => renderPreviewNode(edges[0].targetId), 500);
            }
        }
    };

    function renderPreviewNode(nodeId) {
        const preview = document.getElementById("preview-chat");
        const data = nodesData[nodeId];
        
        if(!data) return;

        // Dynamic Text Replacement
        let msgText = data.config.text || '';
        if (msgText) {
            const matches = msgText.match(/\{\{([^}]+)\}\}/g);
            if (matches) {
                matches.forEach(match => {
                    const varName = match.replace(/[{}]/g, '').trim();
                    const replacement = window.previewVariables[varName] || '';
                    msgText = msgText.replace(match, replacement);
                });
            }
        }

        if (msgText && data.type !== 'button') {
            preview.innerHTML += `
                <div class="bot-message" style="margin-top:10px;">
                    ${msgText}
                </div>
            `;
        }

        if (data.type === 'question') {
            window.previewWaitingNodeId = nodeId;
            window.previewWaitingVariable = data.config.variable || 'answer';
            preview.parentElement.scrollTop = preview.parentElement.scrollHeight;
            return; // STOP! Wait for user input.
        }

        if (data.type === 'condition') {
            const variable = data.config.variable || '';
            const operator = data.config.operator || '==';
            const val = data.config.value || '';
            
            const actualValue = window.previewVariables[variable] || '';
            
            let isTrue = false;
            switch (operator) {
                case '==': isTrue = (actualValue.toLowerCase() == val.toLowerCase()); break;
                case '!=': isTrue = (actualValue.toLowerCase() != val.toLowerCase()); break;
                case '>': isTrue = (parseFloat(actualValue) > parseFloat(val)); break;
                case '<': isTrue = (parseFloat(actualValue) < parseFloat(val)); break;
                case 'contains': isTrue = (actualValue.toLowerCase().includes(val.toLowerCase())); break;
            }
            
            const branch = isTrue ? 'true' : 'false';
            
            // Show a systemic message explaining what condition did
            preview.innerHTML += `
                <div style="margin-top:10px; align-self:center; background:#e5e7eb; color:#6b7280; font-size:11px; padding:4px 8px; border-radius:12px;">
                    Condition Evaluated: ${variable} (${actualValue}) ${operator} ${val} => <b>${branch.toUpperCase()}</b>
                </div>
            `;
            
            const edges = jsPlumb.getConnections({ source: nodeId });
            const matchedEdge = edges.find(e => e.endpoints[0].getParameter('branch') === branch);
            
            preview.parentElement.scrollTop = preview.parentElement.scrollHeight;
            if (matchedEdge) {
                setTimeout(() => renderPreviewNode(matchedEdge.targetId), 1000);
            }
            return;
        }

        if (data.type === 'button' && data.config.buttons) {
            if (msgText) {
                preview.innerHTML += `<div class="bot-message" style="margin-top:10px;">${msgText}</div>`;
            }
            let btnsHtml = `<div style="display:flex; flex-direction:column; gap:5px; margin-top:5px; width: 85%;">`;
            data.config.buttons.forEach((btn, idx) => {
                btnsHtml += `<button style="background: #202c33; color: #00a884; border:none; padding:10px; border-radius:10px; cursor:pointer;" onclick="previewClick('${nodeId}', ${idx}, '${btn}')">${btn}</button>`;
            });
            btnsHtml += `</div>`;
            preview.innerHTML += btnsHtml;
        } else {
            // Find next node automatically if not waiting for button or question
            const edges = jsPlumb.getConnections({ source: nodeId });
            if (edges.length > 0) {
                // Wait a sec then render next
                setTimeout(() => renderPreviewNode(edges[0].targetId), 1000);
            }
        }
        preview.parentElement.scrollTop = preview.parentElement.scrollHeight;
    }

    window.previewClick = (sourceId, btnIndex, text) => {
        const preview = document.getElementById("preview-chat");
        preview.innerHTML += `
            <div class="user-message" style="margin-top:10px;">
                ${text}
            </div>
        `;
        
        // Find connection from this node
        const edges = jsPlumb.getConnections({ source: sourceId });
        if(edges.length > btnIndex) {
            setTimeout(() => renderPreviewNode(edges[btnIndex].targetId), 1000);
        } else if (edges.length > 0) {
            setTimeout(() => renderPreviewNode(edges[0].targetId), 1000);
        }
    };


    /* ================= JSPLUMB INIT ================= */
    jsPlumb.ready(function () {
        jsPlumb.setContainer(flowCanvas);

        const common = {
            connector: ["Bezier", { curviness: 50 }],
            paintStyle: { stroke: "#3b82f6", strokeWidth: 2 },
            endpoint: ["Dot", { radius: 6 }],
            endpointStyle: { fill: "#2563eb" },
            overlays: [
                ["Arrow", { width: 10, length: 10, location: 1 }]
            ],
            maxConnections: -1
        };

        /* ================= DRAG FROM SIDEBAR ================= */
        const dragNodes = document.querySelectorAll(".drag-node");
        dragNodes.forEach(node => {
            // Drag and Drop
            node.addEventListener("dragstart", (e) => {
                e.dataTransfer.setData("type", node.dataset.type);
            });

            // Click to Add & Auto-Connect
            node.addEventListener("click", () => {
                const type = node.dataset.type;
                const newId = "node_" + Date.now();
                let x = 300, y = 100; // Default position
                let sourceNodeId = null;

                if (selectedNode) {
                    const parentDiv = document.getElementById(selectedNode);
                    if (parentDiv) {
                        x = parseInt(parentDiv.style.left) || 300;
                        y = (parseInt(parentDiv.style.top) || 100) + 150; // Spawn 150px below parent
                        sourceNodeId = selectedNode;
                    }
                } else if (window.panzoomInstance) {
                    // Try to center it in current view
                    const pan = window.panzoomInstance.getPan();
                    const scale = window.panzoomInstance.getScale();
                    x = (window.innerWidth / 2 - pan.x) / scale - 100;
                    y = (window.innerHeight / 2 - pan.y) / scale - 50;
                }

                // Ensure it doesn't go off canvas completely
                if (x < 0) x = 50;
                if (y < 0) y = 50;

                createNode(type, x, y, newId, getDefaultConfig(type));

                // Auto Connect logic
                if (sourceNodeId && type !== 'start') {
                    const srcType = nodesData[sourceNodeId].type;
                    
                    if (srcType === 'condition') {
                        // For condition nodes, we find the first empty endpoint (True, then False)
                        const endpoints = jsPlumb.getEndpoints(sourceNodeId) || [];
                        let connected = false;
                        
                        // Try to connect to True first, then False
                        for (let ep of endpoints) {
                            if (ep.isSource && ep.connections.length === 0) {
                                // Position the node slightly left for True, right for False
                                const branchType = ep.parameters ? ep.parameters.branch : null;
                                const newEl = document.getElementById(newId);
                                if (newEl) {
                                    if (branchType === 'true') newEl.style.left = (x - 100) + 'px';
                                    if (branchType === 'false') newEl.style.left = (x + 100) + 'px';
                                }

                                jsPlumb.connect({
                                    sourceEndpoint: ep,
                                    target: newId
                                });
                                connected = true;
                                break;
                            }
                        }
                    } else {
                        // Standard auto-connect for normal nodes using explicit endpoint
                        const eps = jsPlumb.getEndpoints(sourceNodeId) || [];
                        const sourceEp = eps.find(ep => ep.isSource && (!ep.parameters || !ep.parameters.branch));
                        
                        if (sourceEp) {
                            jsPlumb.connect({
                                sourceEndpoint: sourceEp,
                                target: newId
                            });
                        }
                    }
                }

                // Select the new node so user can rapidly chain clicks!
                document.querySelectorAll('.flow-node').forEach(el => el.classList.remove('selected'));
                const newEl = document.getElementById(newId);
                if (newEl) newEl.classList.add('selected');
                selectedNode = newId;
                renderSettings();
            });
        });

        flowCanvas.addEventListener("dragover", (e) => e.preventDefault());
        flowCanvas.addEventListener("drop", (e) => {
            e.preventDefault();
            const type = e.dataTransfer.getData("type");
            if(type) {
                const rect = flowCanvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                createNode(type, x, y, "node_" + Date.now(), getDefaultConfig(type));
            }
        });

        // Deselect when clicking canvas
        flowCanvas.addEventListener("click", (e) => {
            if(e.target === flowCanvas) {
                document.querySelectorAll('.flow-node').forEach(el => el.classList.remove('selected'));
                selectedNode = null;
            }
        });

        /* ================= CREATE NODE ================= */
        function createNode(type, x, y, id, config) {
            nodesData[id] = { id, type, x, y, config };

            const div = document.createElement("div");
            div.className = "flow-node no-pan";
            div.id = id;
            div.style.left = x + "px";
            div.style.top = y + "px";

            let icon = "fa-message";
            let title = "Message";
            let color = "blue";
            let previewText = config.text ? config.text.substring(0, 30) : title + ' Node';

            if (type === "start") {
                icon = "fa-play"; title = "START"; color = "green";
                previewText = config.text ? config.text.substring(0, 30) : 'START Node';
            } else if (type === "question") {
                icon = "fa-circle-question"; title = "Question"; color = "orange";
                previewText = config.text ? config.text.substring(0, 30) : 'Question Node';
            } else if (type === "button") {
                icon = "fa-square-check"; title = "Interactive"; color = "purple";
                previewText = config.text ? config.text.substring(0, 30) : 'Interactive Node';
            } else if (type === "condition") {
                icon = "fa-code-branch"; title = "Condition"; color = "yellow";
                const v = config.variable || '?';
                const o = config.operator || '==';
                const val = config.value || '?';
                previewText = `${v} ${o} ${val}`;
            } else if (type === "api") {
                icon = "fa-plug"; title = "API"; color = "red";
                previewText = 'API Node';
            }

            const deleteHtml = type === 'start' ? '' : `<i class="fa-solid fa-trash delete-node" style="position:absolute; top:-10px; right:-10px; background:red; color:white; padding:5px; border-radius:50%; cursor:pointer; font-size:12px; z-index:10;" onclick="deleteNode('${id}')"></i>`;

            div.innerHTML = `
                ${deleteHtml}
                <div class="node-header ${color}">
                    ${title}
                </div>
                <div class="node-body">
                    <i class="fa-solid ${icon}"></i>
                    <p style="font-size:0.8rem; margin:0; margin-left:8px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width: 120px;">
                        ${previewText}
                    </p>
                </div>
            `;

            flowCanvas.appendChild(div);

            // Add endpoints
            if (type !== 'start') {
                jsPlumb.makeTarget(div, { anchor: "Top", allowLoopback: false, ...common });
            }

            if (type === 'condition') {
                // True endpoint
                jsPlumb.addEndpoint(id, {
                    anchor: [0.3, 1, 0, 1], // Bottom Left-ish
                    isSource: true,
                    maxConnections: 1,
                    endpoint: ["Dot", { radius: 6 }],
                    paintStyle: { fill: "#10b981" },
                    connectorStyle: { stroke: "#10b981", strokeWidth: 2 },
                    cssClass: "no-pan",
                    parameters: { branch: 'true' },
                    overlays: [["Label", { label: "True", location: [0.5, 1.5], cssClass: "endpoint-label", labelStyle: { color: "#10b981", fontWeight: "bold", fontSize: "10px" } }]]
                });
                // False endpoint
                jsPlumb.addEndpoint(id, {
                    anchor: [0.7, 1, 0, 1], // Bottom Right-ish
                    isSource: true,
                    maxConnections: 1,
                    endpoint: ["Dot", { radius: 6 }],
                    paintStyle: { fill: "#ef4444" },
                    connectorStyle: { stroke: "#ef4444", strokeWidth: 2 },
                    cssClass: "no-pan",
                    parameters: { branch: 'false' },
                    overlays: [["Label", { label: "False", location: [0.5, 1.5], cssClass: "endpoint-label", labelStyle: { color: "#ef4444", fontWeight: "bold", fontSize: "10px" } }]]
                });
            } else {
                jsPlumb.addEndpoint(id, {
                    anchor: "Bottom",
                    isSource: true,
                    cssClass: "no-pan",
                    ...common
                });
            }

            makeDraggable(div);
            return div;
        }

        /* ================= LOAD EXISTING DATA ================= */
        if (window.LaravelData.flowData && window.LaravelData.flowData.nodes) {
            const fd = window.LaravelData.flowData;
            fd.nodes.forEach(n => {
                createNode(n.type, n.x, n.y, n.id, n.config);
            });
            // Give DOM time to render before connecting
            setTimeout(() => {
                if (fd.edges) {
                    fd.edges.forEach(edge => {
                        let sourceEp = null;
                        const eps = jsPlumb.getEndpoints(edge.sourceId);
                        
                        if (eps) {
                            if (edge.branch) {
                                sourceEp = eps.find(ep => ep.getParameter('branch') === edge.branch);
                            } else {
                                sourceEp = eps.find(ep => ep.isSource && (!ep.parameters || !ep.parameters.branch));
                            }
                        }
                        
                        if (sourceEp) {
                            jsPlumb.connect({ sourceEndpoint: sourceEp, target: edge.targetId, ...common, paintStyle: sourceEp.paintStyle });
                        } else {
                            jsPlumb.connect({ source: edge.sourceId, target: edge.targetId, ...common });
                        }
                    });
                }
            }, 100);
        } else {
            // New chatbot: Create a Comprehensive Example Template
            createNode("start", 1500, 50, "startNode", { text: "Welcome to BizzCRM Chatbot!" });
            
            // First Button Node
            createNode("button", 1500, 180, "node_btn_main", { text: "Please select a department:", buttons: ["Sales", "Support", "Billing"] });
            
            // ==========================================
            // --- SALES PATH (Index 0): BUDGET LOGIC ---
            // ==========================================
            createNode("question", 200, 320, "node_sales_q", { text: "You selected Sales! What is your estimated budget in dollars?", variable: "budget" });
            
            createNode("condition", 200, 450, "node_s_c1", { variable: "budget", operator: ">", value: "10000" });
            createNode("message", -100, 550, "node_s_m1", { text: "Budget > $10,000: You qualify for the Enterprise Plan!" });
            
            createNode("condition", 200, 600, "node_s_c2", { variable: "budget", operator: ">", value: "5000" });
            createNode("message", -100, 700, "node_s_m2", { text: "Budget > $5,000: You qualify for the Premium Plan!" });
            
            createNode("condition", 200, 750, "node_s_c3", { variable: "budget", operator: ">", value: "1000" });
            createNode("message", -100, 850, "node_s_m3", { text: "Budget > $1,000: You qualify for the Pro Plan." });
            
            createNode("condition", 200, 900, "node_s_c4", { variable: "budget", operator: ">", value: "500" });
            createNode("message", -100, 1000, "node_s_m4", { text: "Budget > $500: You qualify for the Standard Plan." });
            
            createNode("condition", 200, 1050, "node_s_c5", { variable: "budget", operator: ">", value: "100" });
            createNode("message", -100, 1150, "node_s_m5", { text: "Budget > $100: We recommend the Basic Starter Plan." });
            createNode("message", 500, 1150, "node_s_m6", { text: "Budget under $100: You can use our Free Tier!" });


            // ==============================================
            // --- SUPPORT PATH (Index 1): PRIORITY LOGIC ---
            // ==============================================
            createNode("question", 1500, 320, "node_sup_q", { text: "You selected Support. On a scale of 1 to 10, how urgent is this issue?", variable: "priority" });
            
            createNode("condition", 1500, 450, "node_sup_c1", { variable: "priority", operator: ">", value: "8" });
            createNode("message", 1200, 550, "node_sup_m1", { text: "Priority > 8: Transferring to Emergency Agent immediately!" });
            
            createNode("condition", 1500, 600, "node_sup_c2", { variable: "priority", operator: ">", value: "6" });
            createNode("message", 1200, 700, "node_sup_m2", { text: "Priority > 6: Placed in the High Priority Queue." });
            
            createNode("condition", 1500, 750, "node_sup_c3", { variable: "priority", operator: ">", value: "4" });
            createNode("message", 1200, 850, "node_sup_m3", { text: "Priority > 4: Standard Ticket created. ETA 24 hours." });
            
            createNode("condition", 1500, 900, "node_sup_c4", { variable: "priority", operator: ">", value: "2" });
            createNode("message", 1200, 1000, "node_sup_m4", { text: "Priority > 2: Low Priority Ticket. Please check email." });
            
            createNode("condition", 1500, 1050, "node_sup_c5", { variable: "priority", operator: ">", value: "0" });
            createNode("message", 1200, 1150, "node_sup_m5", { text: "Priority 1-2: We recommend reading our Self-Help Docs." });
            createNode("message", 1800, 1150, "node_sup_m6", { text: "Invalid Priority. Please restart chat." });


            // ==================================================
            // --- BILLING PATH (Index 2): ACCOUNT AGE LOGIC ---
            // ==================================================
            createNode("question", 2800, 320, "node_bil_q", { text: "You selected Billing. How many months have you been a customer?", variable: "months" });
            
            createNode("condition", 2800, 450, "node_bil_c1", { variable: "months", operator: ">", value: "60" });
            createNode("message", 2500, 550, "node_bil_m1", { text: "5+ Years! Enjoy our 50% Legacy Loyalty Discount!" });
            
            createNode("condition", 2800, 600, "node_bil_c2", { variable: "months", operator: ">", value: "36" });
            createNode("message", 2500, 700, "node_bil_m2", { text: "3+ Years! Enjoy our 30% Platinum Discount!" });
            
            createNode("condition", 2800, 750, "node_bil_c3", { variable: "months", operator: ">", value: "24" });
            createNode("message", 2500, 850, "node_bil_m3", { text: "2+ Years! Enjoy our 20% Gold Discount!" });
            
            createNode("condition", 2800, 900, "node_bil_c4", { variable: "months", operator: ">", value: "12" });
            createNode("message", 2500, 1000, "node_bil_m4", { text: "1+ Years! Enjoy our 10% Silver Discount." });
            
            createNode("condition", 2800, 1050, "node_bil_c5", { variable: "months", operator: ">", value: "6" });
            createNode("message", 2500, 1150, "node_bil_m5", { text: "6+ Months! Enjoy a 5% Bronze Discount." });
            createNode("message", 3100, 1150, "node_bil_m6", { text: "Standard Billing applies. Thank you!" });
            
            createNode("api", 3100, 1300, "node_bil_api", { text: "GET /api/check-billing" });


            setTimeout(() => {
                // Main branches
                jsPlumb.connect({ source: "startNode", target: "node_btn_main", ...common });
                jsPlumb.connect({ source: "node_btn_main", target: "node_sales_q", ...common });
                jsPlumb.connect({ source: "node_btn_main", target: "node_sup_q", ...common });
                jsPlumb.connect({ source: "node_btn_main", target: "node_bil_q", ...common });

                // Utility function to connect chained conditions
                function connectChain(prefix) {
                    jsPlumb.connect({ source: `node_${prefix}_q`, target: `node_${prefix}_c1`, ...common });
                    for (let i = 1; i <= 5; i++) {
                        const eps = jsPlumb.getEndpoints(`node_${prefix}_c${i}`);
                        if (eps) {
                            const cTrue = eps.find(ep => ep.getParameter('branch') === 'true');
                            const cFalse = eps.find(ep => ep.getParameter('branch') === 'false');
                            if (cTrue) jsPlumb.connect({ source: cTrue, target: `node_${prefix}_m${i}`, ...common, paintStyle: cTrue.paintStyle });
                            if (cFalse) {
                                if (i < 5) {
                                    jsPlumb.connect({ source: cFalse, target: `node_${prefix}_c${i+1}`, ...common, paintStyle: cFalse.paintStyle });
                                } else {
                                    jsPlumb.connect({ source: cFalse, target: `node_${prefix}_m6`, ...common, paintStyle: cFalse.paintStyle });
                                }
                            }
                        }
                    }
                }

                connectChain("s");   // Sales Path
                connectChain("sup"); // Support Path
                connectChain("bil"); // Billing Path

                // Hook up the API node at the very end of billing just to showcase it
                jsPlumb.connect({ source: "node_bil_m6", target: "node_bil_api", ...common });

            }, 400);
        }

        /* ================= SAVE LOGIC ================= */
        window.saveChatbot = () => {
            const btn = document.getElementById('save-btn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
            btn.disabled = true;

            // Gather edges
            const edges = [];
            jsPlumb.getConnections().forEach(conn => {
                let branch = null;
                if (conn.endpoints[0] && conn.endpoints[0].getParameter('branch')) {
                    branch = conn.endpoints[0].getParameter('branch');
                }
                edges.push({
                    sourceId: conn.sourceId,
                    targetId: conn.targetId,
                    branch: branch
                });
            });

            // Gather nodes
            const nodesArray = Object.values(nodesData);

            const getVal = (id, fallback) => document.getElementById(id) ? document.getElementById(id).value : fallback;
            const getCheck = (id, fallback) => document.getElementById(id) ? document.getElementById(id).checked : fallback;
            
            const payload = {
                _token: window.LaravelData.csrfToken,
                whatsapp_project_id: getVal('whatsapp-project-id', window.LaravelData.whatsappProjectId) || null,
                name: getVal('bot-name', window.LaravelData.chatbotName) || 'New Chatbot',
                trigger_type: getVal('trigger-type', window.LaravelData.triggerType) || 'keyword',
                trigger_value: getVal('trigger-value', window.LaravelData.triggerValue) || '',
                is_active: getCheck('set-is-active', window.LaravelData.isActive) ? 1 : 0,
                flow_data: { nodes: nodesArray, edges: edges }
            };

            if (window.LaravelData.isEdit) {
                payload._method = 'PUT';
            }

            fetch(window.LaravelData.saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    alert('Chatbot saved successfully!');
                    if(!window.LaravelData.isEdit) {
                        window.location.href = window.LaravelData.redirectUrl;
                    }
                } else {
                    alert('Error saving chatbot.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('A server error occurred.');
            })
            .finally(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        };

        // Render initial Chatbot settings if no node is selected
        renderSettings();
    });
});

// ==========================================
// --- PAN & ZOOM LOGIC (via Panzoom) ---
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    const canvas = document.getElementById("flowCanvas");
    if(!canvas) return;

    // We wait briefly for panzoom script to load if it's async
    setTimeout(() => {
        if(typeof Panzoom !== 'undefined') {
            window.panzoomInstance = Panzoom(canvas, {
                maxScale: 2,
                minScale: 0.3,
                step: 0.1,
                excludeClass: 'no-pan' // Prevents panning when dragging nodes or endpoints
            });

            // Enable smooth native-like scrolling and zooming over the wrapper
            const wrapper = document.querySelector('.flow-wrapper');
            if(wrapper) {
                wrapper.addEventListener('wheel', function(e) {
                    if (e.ctrlKey || e.metaKey) {
                        // ZOOM if holding Ctrl/Cmd
                        e.preventDefault();
                        window.panzoomInstance.zoomWithWheel(e);
                    } else {
                        // SCROLL (Pan) if using mouse wheel / trackpad
                        e.preventDefault();
                        const currentPan = window.panzoomInstance.getPan();
                        window.panzoomInstance.pan(
                            currentPan.x - e.deltaX,
                            currentPan.y - e.deltaY,
                            { animate: false }
                        );
                    }
                }, { passive: false });
            }

            // Keep jsPlumb math accurate with panzoom scale
            canvas.addEventListener('panzoomzoom', (e) => {
                if(window.jsPlumb) {
                    window.jsPlumb.setZoom(e.detail.scale);
                    window.jsPlumb.repaintEverything();
                }
            });

            window.zoomIn = function() { window.panzoomInstance.zoomIn(); };
            window.zoomOut = function() { window.panzoomInstance.zoomOut(); };
            window.resetZoom = function() { window.panzoomInstance.reset(); };
        }
    }, 500);
});