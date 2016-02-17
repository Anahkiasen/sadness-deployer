<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sadness TasksRunner</title>
    <style>
        body {
            background: #263238;
            padding: 2rem;
            color: #ECEFF1;
            font-family: Monaco, monospace;
            font-size: 12px;
        }

        p {
            margin: 0;
            padding: 0;
        }

        .task {
            opacity: 0.5;
        }

        .task.done {
            opacity: 1;
        }

        .task__command {
            color: white;
            font-weight: bold;
            margin-bottom: 0.25em;
        }

        .task__command small {
            color: #666;
        }

        .task__output {
            margin-bottom: 1em;
        }

        .task__output.success {
            color: #69F0AE;
        }

        .task__output.error {
            color: #FF5252;
        }
    </style>
</head>
<body>
    <main>
        <div class="task" v-for="task in tasks" v-bind:class="{done: task.done}">
            <p class="task__command">
                $ {{ task.command }}
                <small v-if="task.command != task.sanitized">{{ task.sanitized }}</small>
            </p>
            <p class="task__output success" v-if="task.status">{{{ task.output|nl2br }}}</p>
            <p class="task__output error" v-if="!task.status">{{{ task.output|nl2br }}}</p>
        </div>
    </main>

    <script src="//cdnjs.cloudflare.com/ajax/libs/vue/1.0.16/vue.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/vue-resource/0.7.0/vue-resource.min.js"></script>
    <script>
        var app = new Vue({
            el:      'main',
            data:    {
                hash: <?php echo json_encode($hash) ?>,
                taskKey: 0,
                tasks: <?php echo json_encode($tasks) ?>,
            },
            created: function () {
                this.runTask();
            },
            filters: {
                nl2br: function (value) {
                    return ('' + value)
                        .replace(/\n/g, '<br />');
                },
            },
            methods: {
                runTask() {
                    var url = '/deploy/run/' + this.hash + '/' + this.taskKey + window.location.search;
                    this.$http({url: url}).then(function (response) {
                        var task = response.data;
                        task.done = true;

                        this.tasks.$set(this.taskKey, task);
                        this.$set('taskKey', this.taskKey + 1);
                        if (this.taskKey < this.tasks.length) {
                            this.runTask();
                        } else {
                            this.$set('tasks', this.tasks.filter(function (task) {
                                return task.done;
                            }));
                        }
                    });
                },
            },
        });
    </script>
</body>
</html>
