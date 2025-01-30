<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List with Pagination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        let url = 'api_demo.php';
        let currentPage = 1;
        const tasksPerPage = 5;

        document.addEventListener("DOMContentLoaded", function () {
            fetchTasks(currentPage);
        });

        async function fetchTasks(page) {
            currentPage = page;
            fetch(`${url}?page=${page}&limit=${tasksPerPage}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }
                    displayTasks(data.tasks);
                    updatePagination(data.totalPages, data.totalTasks);
                })
                .catch(error => console.error("Error fetching tasks:", error));
        }

        function displayTasks(tasks) {
            const taskList = document.getElementById("taskList");
            taskList.innerHTML = "";
            tasks.forEach(task => {
                const li = document.createElement("li");
                li.className = "list-group-item d-flex justify-content-between align-items-center";

                const taskName = document.createElement("span");
                taskName.textContent = `${task.task_name} - ${task.category}`;

                const editButton = document.createElement("button");
                editButton.textContent = "Edit";
                editButton.className = "btn btn-warning btn-sm mx-1";
                editButton.onclick = () => editTask(task.task_id, task.task_name, task.category);

                const deleteButton = document.createElement("button");
                deleteButton.textContent = "Delete";
                deleteButton.className = "btn btn-danger btn-sm";
                deleteButton.onclick = () => deleteTask(task.task_id);

                li.appendChild(taskName);
                li.appendChild(editButton);
                li.appendChild(deleteButton);
                taskList.appendChild(li);
            });
        }

        function updatePagination(totalPages, totalTasks) {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = "";

            const infoText = document.createElement("p");
            infoText.className = "text-center ";
            infoText.style="font-size:small";
            infoText.textContent = `Total Tasks: ${totalTasks} | Displaying Page ${currentPage} of ${totalPages}`;
            pagination.appendChild(infoText);

            if (totalPages <= 1) return;

            const prevButton = document.createElement("button");
            prevButton.textContent = "Previous";
            prevButton.className = "btn btn-outline-primary mx-1";
            prevButton.disabled = currentPage === 1;
            prevButton.onclick = () => fetchTasks(currentPage - 1);
            pagination.appendChild(prevButton);

            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement("button");
                pageButton.textContent = i;
                pageButton.className = `btn ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'} mx-1`;
                pageButton.onclick = () => fetchTasks(i);
                pagination.appendChild(pageButton);
            }

            const nextButton = document.createElement("button");
            nextButton.textContent = "Next";
            nextButton.className = "btn btn-outline-primary mx-1";
            nextButton.disabled = currentPage === totalPages;
            nextButton.onclick = () => fetchTasks(currentPage + 1);
            pagination.appendChild(nextButton);
        }

        async function addTask() {
            let task_name = document.getElementById("task-name").value.trim();
            let category = document.getElementById("category").value.trim();

            if (!task_name || !category) {
                alert("Please enter task name and select a category.");
                return;
            }

            await fetch(url, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ task_name, category }),
            });

            resetForm();
            fetchTasks(currentPage);
        }

        function editTask(task_id, task_name, category) {
            document.getElementById("task-id").value = task_id;
            document.getElementById("task-name").value = task_name;
            document.getElementById("category").value = category;

            document.getElementById("add-btn").style.display = "none";
            document.getElementById("update-btn").style.display = "block";
        }

        async function updateTask() {
            let task_id = document.getElementById("task-id").value;
            let task_name = document.getElementById("task-name").value.trim();
            let category = document.getElementById("category").value.trim();

            if (!task_id || !task_name || !category) {
                alert("Please fill out all fields.");
                return;
            }

            await fetch(url, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ task_id, task_name, category }),
            });

            resetForm();
            fetchTasks(currentPage);
        }

        async function deleteTask(task_id) {
            if (confirm("Are you sure you want to delete this task?")) {
                await fetch(url, {
                    method: "DELETE",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ task_id }),
                });
                fetchTasks(currentPage);
            }
        }

        function resetForm() {
            document.getElementById("task-id").value = "";
            document.getElementById("task-name").value = "";
            document.getElementById("category").value = "";

            document.getElementById("add-btn").style.display = "block";
            document.getElementById("update-btn").style.display = "none";
        }
    </script>
</head>
<body>
    <div class="container border shadow-lg p-4" style="width: 40%; margin-top: 50px">
        <h3 class="text-center mb-4">To Do List</h3>
        <form onsubmit="return false;">
            <input type="hidden" id="task-id">
            <input type="text" id="task-name" placeholder="Enter task" class="form-control mb-3" required />
            <select class="form-select" id="category" required>
                <option selected disabled>Select Category</option>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
            </select><br />
            <button type="button" id="add-btn" class="btn btn-info w-100 mb-3" onclick="addTask()">Add Task</button>
            <button type="button" id="update-btn" class="btn btn-warning w-100 mb-3" onclick="updateTask()" style="display: none;">Update Task</button>
        </form>
        <h5>Task List</h5>
        <ul id="taskList" class="list-group"></ul>
        <div class="text-center mt-3" id="pagination"></div>
        <a href="login.php" class="btn btn-danger mt-3">Logout</a>
    </div>
</body>
</html>
