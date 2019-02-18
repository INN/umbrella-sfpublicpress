from tools.fablib import *

from fabric.api import task

"""
Getting set up for the first time and using vim?
Run:
    :%s/TKTKPROJECT/project_name/g       # name for the project, used as the database name. This should match the umbrella repository name and the domain.wpengine.com name
    :%s/TKTKENV/YOUR_SITE_ENV_VAR/g      # environment variable slug from INN's secrets repository
"""

"""
Base configuration
"""
env.project_name = 'TKTKPROJECT'
env.hosts = ['localhost', ]
env.sftp_deploy = True # needed for wpengine
env.domain = 'TKTKPROJECT.dev'

"""
Add HipChat info to send a message to a room when new code has been deployed.
"""
env.hipchat_token = ''
env.hipchat_room_id = ''


# Environments
@task
def production():
    """
    Work on production environment
    """
    env.settings    = 'production'
    env.hosts       = [ os.environ[ 'TKTKENV_PRODUCTION_SFTP_HOST' ], ] # ssh host for production.
    env.user        = os.environ[ 'TKTKENV_PRODUCTION_SFTP_USER' ]      # ssh user for production.
    env.password    = os.environ[ 'TKTKENV_PRODUCTION_SFTP_PASSWORD' ]  # ssh password for production.
    env.path        = os.environ[ 'TKTKENV_PRODUCTION_SFTP_PATH' ]      # path on server for production.
    env.domain      = 'TKTKPROJECT.wpengine.com'
    env.port        = '2222'


@task
def staging():
    """
    Work on staging environment
    """
    env.settings    = 'staging'
    env.hosts       = [ os.environ[ 'TKTKENV_STAGING_SFTP_HOST' ], ] # ssh host for staging.
    env.user        = os.environ[ 'TKTKENV_STAGING_SFTP_USER' ]      # ssh user for staging.
    env.password    = os.environ[ 'TKTKENV_STAGING_SFTP_PASSWORD' ]  # ssh password for staging.
    env.path        = os.environ[ 'TKTKENV_STAGING_SFTP_PATH' ]   # path on server for staging.
    env.domain      = 'TKTKPROJECT.staging.wpengine.com'
    env.port        = '2222'

try:
    from local_fabfile import  *
except ImportError:
    pass
