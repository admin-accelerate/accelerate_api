pipeline {
    agent any
    stages {
        stage('Checkout') {
            steps {
                git url: 'https://github.com/admin-accelerate/accelerate_api', branch: 'main', credentialsId: 'github-token'
            }
        }
        stage('Install Dependencies') {
            steps {
                bat 'composer install'
                bat 'npm install'
            }
        }
        stage('Run Tests') {
            steps {
                bat 'php artisan migrate --env=testing'
                bat 'php artisan test --testsuite=Feature'
            }
        }
        stage('Generate Swagger') {
            steps {
                bat 'php artisan l5-swagger:generate'
            }
        }
    }
    post {
        always {
            echo 'Pipeline finished'
        }
        failure {
            mail to: 'djamioufadebi@gmail.com',
                 subject: "Build Failed: ${env.JOB_NAME} #${env.BUILD_NUMBER}",
                 body: "Check ${env.BUILD_URL}"
        }
    }
}