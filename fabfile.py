from tools.fablib import *

from fabric.api import task


"""
Base configuration
"""
env.project_name = 'citylimits'       # name for the project.
env.hosts = ['localhost', ]
env.sftp_deploy = True
env.domain = 'citylimits.dev'


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
    env.hosts       = [ os.environ[ 'CITYLIMITS_PRODUCTION_SFTP_HOST' ], ]   # ssh host for production.
    env.user        = os.environ[ 'CITYLIMITS_PRODUCTION_SFTP_USER' ]        # ssh user for production.
    env.password    = os.environ[ 'CITYLIMITS_PRODUCTION_SFTP_PASSWORD' ]    # ssh password for production.
    env.domain      = 'citylimits.org'
    env.port        = '2222'



@task
def staging():
    """
    Work on staging environment
    """
    env.settings    = 'staging'
    env.hosts       = [ os.environ[ 'CITYLIMITS_STAGING_SFTP_HOST' ], ]   # ssh host for production.
    env.user        = os.environ[ 'CITYLIMITS_STAGING_SFTP_USER' ],       # ssh user for production.
    env.password    = os.environ[ 'CITYLIMITS_STAGING_SFTP_PASSWORD' ]    # ssh password for production.
    env.domain      = 'citylimitsorg.staging.wpengine.com'
    env.port        = '2222'

try:
    from local_fabfile import  *
except ImportError:
    pass
