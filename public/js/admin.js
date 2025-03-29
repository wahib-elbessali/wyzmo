document.addEventListener("DOMContentLoaded", () => {
    let stats = {
      nbProjects: 0,
      nbTasks: 0,
      nbFinishedProjects: 0,
      nbTodoTasks: 0,
      nbInProgressTasks: 0,
      nbCompletedTasks: 0,
      sumProgress: 0
    };

    const fetchAllProjects = async () => {
      try {
        const response = await fetch('/api/admin/projects');
        const { data } = await response.json();
        makeStats(data);
      } catch (error) {
        console.error('Error:', error);
      }
    };

    const makeStats = (projects) => {
      stats.nbProjects = projects.length;
      stats.nbTasks = 0;
      stats.nbFinishedProjects = 0;
      stats.nbTodoTasks = 0;
      stats.nbInProgressTasks = 0;
      stats.nbCompletedTasks = 0;
      stats.sumProgress = 0;

      projects.forEach(project => {
        stats.sumProgress += project.progress || 0;
        if (project.progress === 100) stats.nbFinishedProjects++;
        project.tasks?.forEach(task => {
          stats.nbTasks++;
          switch (task.status.toLowerCase()) {
            case 'todo': stats.nbTodoTasks++; break;
            case 'in_progress': stats.nbInProgressTasks++; break;
            case 'done': stats.nbCompletedTasks++; break;
          }
        });
      });
      updateUI();
    };

    const updateUI = () => {
      document.getElementById("nbProjects").textContent = stats.nbProjects;
      document.getElementById("nbTasks").textContent = stats.nbTasks;
      document.getElementById("nbFinishedProjects").textContent = stats.nbFinishedProjects;

      const totalTasks = stats.nbTasks || 1;
      const pieChart = document.querySelector('.pie-chart');
      const todo = (stats.nbTodoTasks / totalTasks) * 100;
      const inProgress = (stats.nbInProgressTasks / totalTasks) * 100;
      const done = (stats.nbCompletedTasks / totalTasks) * 100;
      const todoStop = todo;
      const inProgressStop = todo + inProgress;
      pieChart.style.setProperty('--todo', `${todoStop}%`);
      pieChart.style.setProperty('--in-progress', `${inProgressStop}%`);

      document.getElementById("todoPercent").textContent = `${Math.round(todo)}%`;
      document.getElementById("inProgressPercent").textContent = `${Math.round(inProgress)}%`;
      document.getElementById("donePercent").textContent = `${Math.round(done)}%`;

      const avgTasks = stats.nbProjects > 0 ? stats.nbTasks / stats.nbProjects : 0;
      const avgProgress = stats.nbProjects > 0 ? stats.sumProgress / stats.nbProjects : 0;
      document.getElementById("avgTasks").textContent = avgTasks.toFixed(2);
      document.getElementById("avgProgress").textContent = avgProgress.toFixed(2);

      const progressBar = document.getElementById("progressBar");
      const comletedLabel = document.getElementById("comletedLabel")
      const percentCompleted = stats.nbProjects > 0 ? (stats.nbFinishedProjects / stats.nbProjects) * 100 : 0;
      progressBar.style.width = `${percentCompleted}%`;
      comletedLabel.innerHTML = `Completed projects (${Math.round(percentCompleted)}%)`
    };

    fetchAllProjects();
  });