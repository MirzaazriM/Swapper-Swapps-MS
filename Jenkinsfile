def project = 'swapper-project-googlecloud-name'
def appName = 'swapper-swapps-backend'
def imageTag = "gcr.io/${project}/${appName}.${env.BUILD_NUMBER}"
def feSvcName = "swapper-swapps-backend-service"

pipeline {
    agent {
        kubernetes {
			label 'sample-app'
			defaultContainer 'jnlp'
			yamlFile 'k8s/pod/pod.yaml'
        }
    }
    stages {
    	stage('Build and push image with container builder') {
    		when { branch 'master' }
    		steps {
    			container('gcloud') {
    				sh "PYTHONUNBUFFERED=1 gcloud container builds submit -t ${imageTag} ."
    			}
    		}
    	}
    	stage('Deploy Production') {
    		when { branch 'master' }
    		steps {
    			container('kubectl') {
    				sh("sed -i.bak 's#gcr.io/cloud-solutions-images/swapper-swapps:1.0.0#${imageTag}#' ./k8s/production/*.yaml")
          			sh("kubectl --namespace=production apply -f k8s/services/")
          			sh("kubectl --namespace=production apply -f k8s/production/")
    			}
    		}
    	}
    }
}

