document.addEventListener("DOMContentLoaded", () => {
    const projectsUrl = "/api/projects/list";
    const tasksUrl = "/api/tasks";
    const createProjectUrl = "/api/project";

    const aboutComponent = document.getElementById("aboutComponent");
    const kanbanComponents = document.getElementById("kanbanComponents");
    const mainComponent = document.getElementById("mainComponent");
    const docsComponent = document.getElementById("docsComponent");
    const chatComponent = document.getElementById("chatComponent");
    const topMain = document.getElementsByClassName("main-top")[0];
    

    let projects = [];
    let currentProjectId = null;
    let currentProjectName = null;
    let selectedCollaborators = new Set();
    let currentCollaborators = [];

    kanbanComponents.style.display = "none";
    mainComponent.style.display = "block";
    aboutComponent.style.display = "none";
    docsComponent.style.display = "none";
    chatComponent.style.display = "none"

    function getColor(input) {
        let sum = 0;
        for (let i = 0; i < input.length; i++) {
            sum += input.charCodeAt(i);
        }
        const index = sum % 6;
        const colors = ["#aae5af", "#f5d99a", "#f1879e", "#d3e3f3", "#ddf0b9", "#c0f0be"];
        return colors[index];
    }

    const profilePicture = () => {
        const user = JSON.parse(localStorage.getItem("user"));
        const profileButton = document.getElementById("profileButton");
        profileButton.innerHTML = `<div style="background-color: ${getColor(user.user_id)}"></div> ${user.first_name[0].toUpperCase()}`;
    };

    const fetchProjects = async () => {
        try {
            const response = await fetch(projectsUrl);
            const data = await response.json();
            if (!response.ok) throw data;
            projects = data.data;
            populateProjects();
        } catch (error) {
            console.log(error);
        }
    };

    const fetchTasks = async (projectName) => {
        try {
            const response = await fetch(`/api/tasks/${currentProjectId}`);
            const result = await response.json();
            const { data } = result;
            if (!response.ok) throw data;

            populateTasks(data, projectName);
            kanbanComponents.style.display = "block";
            mainComponent.style.display = "none";
            aboutComponent.style.display = "none";
            docsComponent.style.display = "none";
            chatComponent.style.display = "none";
        } catch (error) {
            console.error('Error fetching tasks:', error);
        }
    };

    const fetchCollaborators = async (projectId) => {
        try {
            const response = await fetch(`/api/collaborator/list/${projectId}`);
            const data = await response.json();
            if (!response.ok) throw data;
            currentCollaborators = data.data;
            populateCollaborators(data.data);
        } catch (error) {
            console.log('Error fetching collaborators:', error);
        }
    };

    const fetchDocs = async (projectId) => {
        try {
            const docsList = document.getElementById("docsList");
            docsList.innerHTML = ''
            const response = await fetch(`/api/docs/${projectId}`);
            const data = await response.json();
            if (!response.ok) throw data;
            populateDocs(data.data)
        } catch (error) {
            console.log('Error fetching docs:', error);
        }
    }

    const displayProject = async (project) => {
        currentProjectId = project.project_id;
        document.getElementById("title").innerHTML = project.name;
        document.getElementById("description").innerHTML = project.description;

        await fetchCollaborators(currentProjectId);

        try {
            const response = await fetch(`/api/progress/${project.project_id}`);
            const data = await response.json();
            if (!response.ok) throw data;

            document.getElementById("progressBarInk").style.width = `${data.data.progress}%`;
            document.getElementById("progress").innerHTML = `Progress: ${data.data.progress}%`;
        } catch (error) {
            console.log(error);
        }

        aboutComponent.style.display = "flex";
        aboutComponent.style.flexDirection = "column";
        topMain.style.display = "block";
        kanbanComponents.style.display = "none";
        mainComponent.style.display = "none";
        docsComponent.style.display = "none";
        chatComponent.style.display = "none";
    };

    const createProject = async (projectName, projectdescription, startingDate, endingDate) => {
        try {
            const response = await fetch(createProjectUrl, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ 
                    name: projectName, 
                    description: projectdescription, 
                    date_debut: startingDate, 
                    date_fin: endingDate 
                }),
            });
            const data = await response.json();
            document.getElementById("projectModal").style.display = 'none';
            document.getElementById("projectNameInput").value = "";
            document.getElementById("projectTextInput").value = "";
            document.getElementById('startingDate').value = "";
            document.getElementById("endingDate").value = "";
            fetchProjects();
        } catch (error) {
            console.log(error);
        }
    };

    const populateDocs = (docs) => {
        docs.forEach( (doc) => {
            const docsList = document.getElementById("docsList");
            const docDiv = document.createElement('div')
            docDiv.innerHTML =  `
                <div class="docElement borderv${Math.floor(Math.random() * 3) + 1}">
                    <div style="background-color: ${getColor(doc)}"></div>
                    <a href="/../${doc}" download> ${doc.split("/")[3]} </a>
                <div>
            `
            docsList.appendChild(docDiv)
            console.log(doc.split("/")[3])
        })
    }

    const populateProjects = () => {
        const projectsList = document.querySelector("nav ul");
        const projectsSearch = document.getElementById("projectsSearch").value;
        const filteredProjects = projects.filter((project) =>
            !projectsSearch || project.name.toUpperCase().includes(projectsSearch.toUpperCase())
        );

        projectsList.innerHTML = "";
        filteredProjects.forEach(project => {
            const li = document.createElement("li");
            const details = document.createElement("details");
            const summary = document.createElement("summary");
            summary.className = "project-name";
            summary.textContent = project.name;
            summary.id = project.project_id;
            summary.addEventListener("click", () => displayProject(project));

            const workspaceChat = document.createElement("div");
            workspaceChat.className = "workspace-chat";

            const workspaceButton = document.createElement("button");
            workspaceButton.className = "borderv1";
            workspaceButton.textContent = "Workspace";
            workspaceButton.addEventListener("click", () => {
                currentProjectName = project.name;
                currentProjectId = project.project_id;
                fetchTasks(project.name);
            });

            const chatButton = document.createElement("button");
            chatButton.className = "borderv2";
            chatButton.textContent = "Chat";
            chatButton.addEventListener("click", () => {
                currentProjectName = project.name;
                currentProjectId = project.project_id;

                kanbanComponents.style.display = "none";
                mainComponent.style.display = "none";
                aboutComponent.style.display = "none";
                docsComponent.style.display = "none";
                chatComponent.style.display = "block";

                const messageList = document.getElementById('messageList');
                messageList.scrollTop = messageList.scrollHeight;

                loadMessages(currentProjectId);
                chatConnect(project.project_id);
            })

            const docs = document.createElement("button");
            docs.className = "borderv2";
            docs.textContent = "Docs";
            docs.addEventListener("click", () => {
                currentProjectName = project.name;
                currentProjectId = project.project_id;

                fetchDocs(project.project_id);

                kanbanComponents.style.display = "none";
                mainComponent.style.display = "none";
                aboutComponent.style.display = "none";
                docsComponent.style.display = "block";
                chatComponent.style.display = "none";


            });

            workspaceChat.appendChild(workspaceButton);
            workspaceChat.appendChild(chatButton);
            workspaceChat.appendChild(docs);
            details.appendChild(summary);
            details.appendChild(workspaceChat);
            li.appendChild(details);
            projectsList.appendChild(li);
        });
    };

    const populateCollaborators = (collaborators) => {
        const contributorsList = document.getElementById('contributorsList');
        const colaboratorSearch = document.getElementById("colaboratorSearch").value;
        const filteredcollaborators = currentCollaborators.filter((project) =>
            !colaboratorSearch || project.email.toUpperCase().includes(colaboratorSearch.toUpperCase())
        );
        contributorsList.innerHTML = '';

        filteredcollaborators.forEach(collaborator => {
            const contributorDiv = document.createElement('div');
            contributorDiv.className = `contributor-item`;
            contributorDiv.style.display = 'flex';
            contributorDiv.style.alignItems = 'center';
            contributorDiv.style.marginBottom = '10px';

            contributorDiv.innerHTML = `
                <div class="circlev${Math.floor(Math.random() * 3) + 1}" 
                     style="display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 10px;">
                    <div style="background-color: ${getColor(collaborator.user_id)};"></div>
                    ${collaborator.first_name[0].toUpperCase()}
                </div>
                <div class="contributor-info" style="flex-grow: 1;">
                    <span style="font-size: 24px; margin-right: 8px">${collaborator.first_name} ${collaborator.last_name}</span>
                    <span style="opacity: 0.7">${collaborator.email}</span>
                </div>
            `;

            // representative toggle
        const repButton = document.createElement('button');
        repButton.className = `borderv${Math.floor(Math.random() * 3) + 1}`;
        repButton.style.paddingTop = "0"
        repButton.style.paddingBottom = "0"
        repButton.textContent = collaborator.is_representative ? "Remove representative" : "Make representative";
        
        repButton.addEventListener('click', async () => {
            try {
                const response = await fetch('/api/representative', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        project_id: currentProjectId,
                        user_id: collaborator.user_id
                    })
                });
                
                const result = await response.json();
                if (response.ok) {
                    repButton.textContent = result.data.is_representative ? "Remove representative" : "Make representative";
                }
            } catch (error) {
                console.error('Error toggling representative:', error);
            }
        });
        
        contributorDiv.appendChild(repButton);
        
            contributorsList.appendChild(contributorDiv);
        });
    };

    const populateTasks = (tasks, projectName) => {
        const title = document.getElementById("title");
        title.innerHTML = projectName;

        const todo = document.getElementById("todoTasks");
        const inProgress = document.getElementById("inProgressTasks");
        const completed = document.getElementById("completedTasks");

        [todo, inProgress, completed].forEach(div => div.innerHTML = "");

        tasks.forEach(task => {
            const taskElement = document.createElement("div");
            taskElement.className = `borderv${Math.floor(Math.random() * 3) + 1}`;
            taskElement.draggable = true;

            const borderDiv = document.createElement("div");
            const taskHeading = document.createElement("h2");
            taskHeading.textContent = task.name;
            const taskParagraph = document.createElement("p");
            taskParagraph.textContent = task.description;
            taskParagraph.className = "truncate";

            taskElement.appendChild(borderDiv);
            taskElement.appendChild(taskHeading);
            taskElement.appendChild(taskParagraph);

            const importanceDiv = document.createElement("div");
            importanceDiv.className = `importance circlev${Math.floor(Math.random() * 3) + 1}`;
            switch(task.priority.toLowerCase()) {
                case 'low':
                    importanceDiv.style.backgroundColor = "rgb(170, 229, 175)";
                    break;
                case 'medium':
                    importanceDiv.style.backgroundColor = "rgb(245, 217, 154)";
                    break;
                case 'high':
                    importanceDiv.style.backgroundColor = "rgb(241, 135, 158)";
                    break;
                default:
                    importanceDiv.style.backgroundColor = "rgb(165, 209, 168)";
            }
            taskElement.appendChild(importanceDiv);

            addClickListeners(task, taskElement);

            switch (task.status) {
                case "todo":
                    todo.appendChild(taskElement);
                    break;
                case "in_progress":
                    inProgress.appendChild(taskElement);
                    break;
                case "done":
                    completed.appendChild(taskElement);
                    break;
                default:
                    todo.appendChild(taskElement);
            }
        });
    };

    // Event Listeners
    const newProject = document.getElementById("newProject");
    const projectModal = document.getElementById("projectModal");
    newProject.addEventListener("click", () => projectModal.style.display = "block");

    window.addEventListener("click", (event) => {
        if (event.target === projectModal) projectModal.style.display = "none";
        if (event.target === taskModal) taskModal.style.display = "none";
        if (event.target === colabModal) colabModal.style.display = "none";
        if (event.target === taskCreationModal) taskCreationModal.style.display = 'none';
    });

    document.getElementById("newProjectButton").addEventListener("click", () => {
        const projectName = document.getElementById("projectNameInput").value.trim();
        const projectdescription = document.getElementById("projectTextInput").value.trim();
        const startingDate = document.getElementById('startingDate').value;
        const endingDate = document.getElementById("endingDate").value;

        if (projectName && projectdescription && startingDate && endingDate) {
            createProject(projectName, projectdescription, startingDate, endingDate);
        }
    });

    document.getElementById("projectsSearch").addEventListener("keyup", populateProjects);

    document.getElementById("colaboratorSearch").addEventListener("keyup", populateCollaborators);

    const colabModalButton = document.getElementById("addContributor");
    const colabModal = document.getElementById("colaboratorModal");
    colabModalButton.addEventListener("click", () => colabModal.style.display = "block");

    document.getElementById('collaboratorSearchInput').addEventListener('input', async (e) => {
        const searchTerm = e.target.value.trim();
        if (!searchTerm) return;

        try {
            const response = await fetch(`/api/collaborator/search/${searchTerm}`);
            const result = await response.json();
            const resultsContainer = document.getElementById('searchResults');
            resultsContainer.innerHTML = '';

            result.data.forEach(user => {
                const userDiv = document.createElement('div');
                userDiv.className = `borderv${Math.floor(Math.random() * 3) + 1} collaborator-result`;
                userDiv.innerHTML = `<span>${user.first_name} ${user.last_name} (${user.email})</span>`;
                userDiv.addEventListener('click', () => {
                    userDiv.classList.toggle('selected');
                    selectedCollaborators.has(user.user_id) 
                        ? selectedCollaborators.delete(user.user_id)
                        : selectedCollaborators.add(user.user_id);
                });
                resultsContainer.appendChild(userDiv);
            });
        } catch (error) {
            console.error('Search error:', error);
        }
    });

    document.getElementById('addCollaboratorsButton').addEventListener('click', async () => {
        if (!currentProjectId || selectedCollaborators.size === 0) return;

        try {
            await fetch('/api/collaborator', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    project_id: currentProjectId,
                    collaborator_list: Array.from(selectedCollaborators)
                })
            });
            selectedCollaborators.clear();
            colabModal.style.display = "none";
            fetchCollaborators(currentProjectId);
            const deselect = document.getElementsByClassName('selected');
            Array.from(deselect).forEach((i) => {
                i.classList.remove('selected');
            });
        } catch (error) {
            console.error('Submission error:', error);
        }
    });

    const profileButton = document.getElementById("profileButton");
    const profilePopup = document.getElementById("profilePopup");
    profileButton.addEventListener("click", (event) => {
        const buttonRect = profileButton.getBoundingClientRect();
        profilePopup.style.top = `${buttonRect.bottom + window.scrollY + 6}px`;
        profilePopup.style.left = `${buttonRect.left + window.scrollX - 50}px`;
        profilePopup.style.display = profilePopup.style.display === "flex" ? "none" : "flex";
    });

    const profile = document.getElementById("profile");
    profile.addEventListener("click", () => {
      window.location.href = "/profile";
    })

    document.getElementById("logout").addEventListener("click", async () => {
        try {
            await fetch("/api/auth/logout");
            window.location.href = "/workspace";
        } catch (error) {
            console.log(error);
        }
    });

    const taskCreationModal = document.getElementById('taskCreationModal');
    const createTaskBtn = document.getElementById('createTaskButton');
    document.getElementById('newTask').addEventListener('click', () => {
        taskCreationModal.style.display = 'block';
        populateAssigneeDropdown();
    });

    async function populateAssigneeDropdown() {
        try {
            const response = await fetch(`/api/collaborator/list/${currentProjectId}`);
            const result = await response.json();
            const assigneeSelect = document.getElementById('assigneeSelect');
            assigneeSelect.innerHTML = '<option value="">Select Assignee</option>';
            
            result.data.forEach(collab => {
                const option = document.createElement('option');
                option.value = collab.collaborator_id;
                option.textContent = `${collab.first_name} ${collab.last_name}`;
                assigneeSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading collaborators:', error);
        }
    }

    createTaskBtn.addEventListener('click', async () => {
        const taskData = {
            name: document.getElementById('taskName').value.trim(),
            description: document.getElementById('taskDescription').value.trim(),
            start_date: document.getElementById('taskStartDate').value,
            end_date: document.getElementById('taskEndDate').value,
            priority: document.getElementById('taskPriority').value,
            assigned_to: document.getElementById('assigneeSelect').value,
            status: 'todo'
        };

        try {
            await fetch(`/api/tasks/${currentProjectId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(taskData)
            });
            taskCreationModal.style.display = 'none';
            document.getElementById('taskName').value = '';
            document.getElementById('taskDescription').value = '';
            document.getElementById('taskStartDate').value = '';
            document.getElementById('taskEndDate').value = '';
            document.getElementById('taskPriority').value = 'low';
            fetchTasks(currentProjectName);
        } catch (error) {
            console.error('Task creation failed:', error);
        }
    });

    // Task Modal Handling
    const taskModal = document.getElementById("taskModal");
    
    function addClickListeners(task, taskElement) {
        taskElement.addEventListener("click", () => {
            document.getElementById("taskModalTitle").textContent = task.name;
            document.getElementById("taskModalDescription").textContent = task.description;

            const priorityIndicator = document.getElementById("taskPriorityIndicator");
            switch(task.priority.toLowerCase()) {
                case 'low': priorityIndicator.style.backgroundColor = "#aae5af"; break;
                case 'medium': priorityIndicator.style.backgroundColor = "#f5d99a"; break;
                case 'high': priorityIndicator.style.backgroundColor = "#f1879e"; break;
                default: priorityIndicator.style.backgroundColor = "#aae5af";
            }

            const assignee = currentCollaborators.find(c => c.collaborator_id === task.assigned_to);
            document.getElementById("taskModalAssignee").textContent = 
                assignee ? `${assignee.first_name} ${assignee.last_name}` : 'Unassigned';

            document.getElementById("taskModalStartDate").textContent = 
                new Date(task.start_date).toLocaleDateString();
            document.getElementById("taskModalEndDate").textContent = 
                new Date(task.end_date).toLocaleDateString();

            taskModal.style.display = "flex";
        });
    }

    document.getElementById('newDoc').addEventListener('click', function() {
        document.getElementById('fileInput').click();
    });
    document.getElementById('fileInput').addEventListener('change', function() {
        const file = this.files[0];
    
        if (file) {
            const projectId = currentProjectId;
            sendFile(projectId, file);
        }
    });

    function sendFile(projectId, file) {
        const path = `${projectId}:${file.name}`;
        const formData = new FormData();
        formData.append('file', file);
    
        // Send the file to the API
        fetch(`/api/docs/${path}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            fetchDocs(currentProjectId);
            console.log('File uploaded successfully:', data);
        })
        .catch(error => {
            console.error('Error uploading file:', error);
        });
    }

    let channel;
    const user = JSON.parse(localStorage.getItem("user"));
    async function loadMessages(projectId) {
        try {
            const response = await fetch(`/api/messages/${projectId}`);
            const messages = await response.json();
            
            messages.data.forEach((msg) => {
                const messageDiv = document.createElement("div");
                messageDiv.innerHTML = `
                    <div style="background-color:${getColor(msg.sender_id)}; opacity:0.75;"></div>
                    ${ msg.sender_id !== user.user_id ? '<strong>'+ msg.first_name + ' '  + msg.last_name + '</strong>' : "" }
                    <p style="font-size: x-large">${msg.message}</p>
                `
                messageDiv.className = "borderv2";

                messageDiv.style.marginLeft = msg.sender_id === user.user_id ? "auto" : "0";
                messageDiv.style.marginRight = msg.sender_id === user.user_id ? "0" : "auto";

                const messageList = document.getElementById('messageList');
                messageList.appendChild(messageDiv)
                messageList.scrollTop = messageList.scrollHeight;


            })

            
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }
    const chatConnect = (projectId) => {

        document.getElementById('messageList').innerHTML = ""

        if(channel){
            channel.unsubscribe()
        }else{
            const ably = new Ably.Realtime("tAWqRA.cS1w_w:ln3lJTSJJrf--gSctoLc7MFzzjE5776P5rsAExh3c_o");
            channel = ably.channels.get(projectId);
        }

        



        channel.subscribe("message", function(message) {
            console.log("Received:", message.data);
            const { senderId, snederName, text } = message.data;

            const messageDiv = document.createElement("div");

            messageDiv.innerHTML = `
                <div style="background-color:${getColor(senderId)}; opacity:0.75;"></div>
                 ${ senderId !== user.user_id ? '<strong>'+ snederName + '</strong>' : "" }
                <p style="font-size: x-large">${text}</p>
            `
            messageDiv.className = "borderv2"
            
            messageDiv.style.marginLeft = senderId === user.user_id ? "auto" : "0";
            messageDiv.style.marginRight = senderId === user.user_id ? "0" : "auto";

            const messageList = document.getElementById('messageList');
            messageList.appendChild(messageDiv)
            messageList.scrollTop = messageList.scrollHeight;
        });
        
    }
    async function sendMessage() {
        const senderId = user.user_id;
        const snederName = user.first_name + " " + user.last_name
        const text = document.getElementById("message").value;
        
        const messageData = { senderId, snederName, text };

        if (text) {
            try {
                let mes = await fetch(`/api/messages/${currentProjectId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                });
                mes = await mes.json()
                console.log(mes)
                channel.publish("message", messageData);
            } catch (error) {
                console.error('Error sending message:', error);
            }
        }
    }
    document.getElementById("sendMessage")
    .addEventListener("click", () => {
        sendMessage()
        document.getElementById("message").value = ""
    })

    // Initial Setup
    fetchProjects();
    profilePicture();
});