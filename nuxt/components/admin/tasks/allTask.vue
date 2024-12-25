<script>
export default {
  name: "allTask",
  props : ['task_all'] ,
}
</script>

<template>
  <div>
    <section class="scrum-task">
      <div class="container">
        <div class="parent-scrum-task">
          <article v-for="(task , index) in task_all" :key="index"  class="scrum-task-item">
            <div class="parent-title-calendar">
              <div class="parent-title-scrum-task-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="" class="bi bi-tags" viewBox="0 0 16 16">
                  <path d="M3 2v4.586l7 7L14.586 9l-7-7zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586z"/>
                  <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1z"/>
                </svg>
                <p>
                  {{ task.title }}
                </p>
              </div>
              <div class="parent-calendar-scrum-task-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="" class="bi bi-calendar2" viewBox="0 0 16 16">
                  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/>
                  <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
                <span>{{ task.created_at }}</span>
              </div>
            </div>
            <div class="division-labor">
              <div class="creation">
                <Avatar   :src="`${$env.UPLOAD_URL}${task.user_owner.avatar}`"  />
                <span>{{ task.user_owner.name }}</span>
              </div>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="" class="bi bi-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
              </svg>
              <div class="recipient">
                <Avatar v-for="(img , index) in task.user_list" :key="index"  :src="`${$env.UPLOAD_URL}${img.avatar}`"  />
              </div>
              <div class="contract">

              </div>
            </div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>

.parent-scrum-task{
  display: flex;
  align-items: center;
  width: 100%;
  flex-direction: column;
  gap: 5px;
}
.parent-scrum-task article{
  display: flex;
  align-items: center;
  width: 100%;
  gap: 30px;
  padding: 5px 10px;
  background: #f8f5f4;
  border: 1px solid #eee;
  border-radius: 12px;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,.03);
}
.parent-scrum-task article p{
  margin-bottom: 0;
  color: #333;
  font-size: 16px;
}
.division-labor{
  display: flex;
  align-items: center;
}
.parent-title-calendar{
  display: flex;
  align-items: center;
  gap: 20px;
}
.parent-title-scrum-task-item{
  display: flex;
  align-items: center;
  gap: 5px;
}
.parent-calendar-scrum-task-item{
  display: flex;
  align-items: center;
  gap: 5px;
}
.parent-calendar-scrum-task-item span{
  font-size: 16px;
  color: gray;
}
.division-labor{
  display: flex;
  align-items: center;
  gap: 30px;
}
.creation{
  display: flex;
  align-items: center;
  gap: 5px;
}
.creation img{
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid #ccc;
}
.creation span{
  font-size: 14px;
  color: gray;
}
.bi-arrow-left{
  width: 30px;
  height: 30px;
  fill: gray;
}
.recipient{
  display: flex;
  align-items: center;
  gap: 3px;
}
.recipient img{
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid #ccc;
}
.bi-calendar2{
  fill: grey;
}
</style>
